<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Snap;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Snap as ESnap;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    implements \Praxigento\Downline\Service\ISnap
{
    /** @var \Praxigento\Downline\Repo\Dao\Snap */
    private $daoSnap;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Downline\Repo\Dao\Snap $daoSnap
    )
    {
        $this->logger = $logger;
        $this->hlpPeriod = $hlpPeriod;
        $this->daoSnap = $daoSnap;
    }

    /**
     * Compose $result array as array of elements:
     * [
     *  ESnap::A_CUSTOMER_ID,
     *  ESnap::A_PARENT_ID,
     *  ESnap::A_DEPTH,
     *  ESnap::A_PATH
     * ]
     * to insert into DB as record in prxgt_dwnl_snap.
     *
     * @param      $result
     * @param      $tree
     * @param null $parentId
     */
    private function _composeSnapData(&$result, $tree, $parentId = null)
    {
        foreach ($tree as $custId => $children) {
            if (is_null($parentId)) {
                /* this is root node */
                $result[$custId] = [
                    ESnap::A_CUSTOMER_ID => $custId,
                    ESnap::A_PARENT_ID => $custId,
                    ESnap::A_DEPTH => Cfg::INIT_DEPTH,
                    ESnap::A_PATH => Cfg::DTPS
                ];
            } else {
                $parentData = $result[$parentId];
                $result[$custId] = [
                    ESnap::A_CUSTOMER_ID => $custId,
                    ESnap::A_PARENT_ID => $parentId,
                    ESnap::A_DEPTH => $parentData[ESnap::A_DEPTH] + 1,
                    ESnap::A_PATH => $parentData[ESnap::A_PATH] . $parentId . Cfg::DTPS
                ];
            }
            if (sizeof($children) > 0) {
                $this->_composeSnapData($result, $children, $custId);
            }
        }
    }

    /**
     * Extend minimal Downline Tree Data (customer & parent) with depth and path.
     *
     * @param Request\ExpandMinimal $request
     *
     * @return Response\ExpandMinimal
     *
     * @deprecated use \Praxigento\Downline\Api\Helper\Downline::expandMinimal instead
     */
    public function expandMinimal(Request\ExpandMinimal $request)
    {
        $result = new Response\ExpandMinimal();
        $keyCustomerId = $request->getKeyCustomerId();
        $keyParentId = $request->getKeyParentId();
        $treeIn = $request->getTree();
        /**
         * Validate tree consistency: all parents should be customers too, create map for customers with invalid
         * parents to set this entries as orphans.
         */
        $mapCusts = []; // registry for all customers.
        foreach ($treeIn as $ndx => $item) {
            if (is_null($keyCustomerId)) {
                $custId = $ndx;
            } else {
                $custId = is_array($item) ? $item[$keyCustomerId] : $item->get($keyCustomerId);
            }
            $mapCusts[] = $custId;
        }
        $mapOrphans = []; // registry for orphan customers.
        foreach ($treeIn as $ndx => $item) {
            if (is_null($keyCustomerId)) {
                $custId = $ndx;
            } else {
                $custId = is_array($item) ? $item[$keyCustomerId] : $item->get($keyCustomerId);
            }

            $parentId = !is_array($item) ? $item : $item[$keyParentId];
            if (!in_array($parentId, $mapCusts)) {
                $msg = "Parent #$parentId for customer #$custId is not present in the minimal tree.";
                $msg .= " Customer #$custId is set as orphan.";
                $this->logger->warning($msg);
                $mapOrphans[] = $custId;
            }
        }
        /* create tree (see http://stackoverflow.com/questions/2915748/how-can-i-convert-a-series-of-parent-child-relationships-into-a-hierarchical-tre) */
        $flat = [];
        $tree = [];
        foreach ($treeIn as $ndx => $item) {
            $custId = is_null($keyCustomerId) ? $ndx : $item[$keyCustomerId];
            $parentId = !is_array($item) ? $item : $item[$keyParentId];
            /* filter orphans */
            if (in_array($custId, $mapOrphans)) {
                $parentId = $custId;
            }
            /* map customers into tree */
            if (!isset($flat[$custId])) {
                $flat[$custId] = [];
            }
            if ($custId != $parentId) {
                $flat[$parentId][$custId] =& $flat[$custId];
            } else {
                /* root node */
                $tree[$custId] =& $flat[$custId];
            }
        }
        /* populate tree with depth/path/... and compose array to insert into DB  */
        $snapData = [];
        $this->_composeSnapData($snapData, $tree);
        $result->setSnapData($snapData);
        $result->markSucceed();
        return $result;
    }

    /**
     * Select downline tree state on the given datestamp.
     *
     * @param Request\GetStateOnDate $request
     *
     * @return Response\GetStateOnDate
     */
    public function getStateOnDate(Request\GetStateOnDate $request)
    {
        $result = new Response\GetStateOnDate();
        $this->logger->info("'Get Downline Tree state' operation is requested.");
        $dateOn = $request->getDatestamp();
        $addCountryCode = (bool)$request->getAddCountryCode();
        if (is_null($dateOn)) {
            $dateOn = $this->hlpPeriod->getPeriodCurrent();
        }
        $rows = $this->daoSnap->getStateOnDate($dateOn, $addCountryCode);
        $result->set($rows);
        $result->markSucceed();
        $this->logger->info("'Get Downline Tree state' operation is completed.");
        return $result;
    }

}