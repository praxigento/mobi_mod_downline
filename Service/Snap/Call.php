<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Snap;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer as ECust;
use Praxigento\Downline\Repo\Data\Snap as ESnap;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Downline\Service\ISnap
{
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    private $daoChange;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoCust;
    /** @var \Praxigento\Downline\Repo\Dao\Snap */
    private $daoSnap;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Downline\Service\Snap\Sub\CalcSimple */
    private $subCalc;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Downline\Repo\Dao\Change $daoChange,
        \Praxigento\Downline\Repo\Dao\Customer $daoCust,
        \Praxigento\Downline\Repo\Dao\Snap $daoSnap,
        \Praxigento\Downline\Service\Snap\Sub\CalcSimple $subCalc
    )
    {
        parent::__construct($logger, $manObj);
        $this->hlpPeriod = $hlpPeriod;
        $this->daoChange = $daoChange;
        $this->daoCust = $daoCust;
        $this->daoSnap = $daoSnap;
        $this->subCalc = $subCalc;
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
     * Calculate downline snapshots up to requested date (including).
     *
     * @param Request\Calc $request
     *
     * @return Response\Calc
     */
    public function calc(Request\Calc $request)
    {
        $result = new Response\Calc();
        $this->logger->info("New downline snapshot calculation is requested.");
        /* get the last date with calculated snapshots */
        $reqLast = new Request\GetLastDate();
        /** @var  $resp Response\GetLastDate */
        $respLast = $this->getLastDate($reqLast);
        $dsLast = $respLast->getLastDate();
        /* clean snapshot on the last date (MOBI-956) */
        $where = ESnap::A_DATE . '>=' . $dsLast;
        $this->daoSnap->delete($where);
        /* get the snapshot on the last date */
        $snapshot = $this->getSnap($dsLast);
        /* get change log for the period */
        $tsFrom = $this->hlpPeriod->getTimestampFrom($dsLast);
        $periodTo = $this->hlpPeriod->getPeriodCurrent();
        $tsTo = $this->hlpPeriod->getTimestampTo($periodTo);
        $changelog = $this->daoChange->getChangesForPeriod($tsFrom, $tsTo);
        /* calculate snapshots for the period */
        $updates = $this->subCalc->calcSnapshots($snapshot, $changelog);
        /* save new snapshots in DB */
        $this->daoSnap->saveCalculatedUpdates($updates);
        /* update actual state of the downline */
        $this->updateDownline($updates);
        /* finalize processing */
        $result->markSucceed();
        return $result;
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
     * Calculate the last date for existing downline snap or the "yesterday" for the first change log entry.
     *
     * @param Request\GetLastDate $request
     *
     * @return Response\GetLastDate
     */
    public function getLastDate(Request\GetLastDate $request)
    {
        $result = new Response\GetLastDate();
        $this->logger->info("'Get Last Data' operation is requested.");
        /* get the maximal date for existing snapshot */
        $snapMaxDate = $this->daoSnap->getMaxDatestamp();
        if ($snapMaxDate) {
            /* there is snapshots data */
            $result->set([Response\GetLastDate::LAST_DATE => $snapMaxDate]);
            $result->markSucceed();
        } else {
            /* there is no snapshot data yet, get change log minimal date  */
            $changelogMinDate = $this->daoChange->getChangelogMinDate();
            if ($changelogMinDate) {
                $period = $this->hlpPeriod->getPeriodCurrent($changelogMinDate);
                $dayBefore = $this->hlpPeriod->getPeriodPrev($period);
                $this->logger->info("The last date for downline snapshot is '$dayBefore'.");
                $result->set([Response\GetLastDate::LAST_DATE => $dayBefore]);
                $result->markSucceed();
            }
        }
        $this->logger->info("'Get Last Data' operation is completed.");
        return $result;
    }

    private function getSnap($datestamp)
    {
        $result = [];
        $snapshot = $this->daoSnap->getStateOnDate($datestamp);
        foreach ($snapshot as $one) {
            $item = new ESnap($one);
            $custId = $item->getCustomerId();
            $result[$custId] = $item;
        }
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

    private function updateDownline($updates)
    {
        /* compress all updates by customer */
        $downline = [];
        foreach ($updates as $date => $updatesByDate) {
            /** @var ESnap $one */
            foreach ($updatesByDate as $one) {
                $custId = $one->getCustomerId();
                $parentId = $one->getParentId();
                $depth = $one->getDepth();
                $path = $one->getPath();
                $entry = new ECust();
                $entry->setCustomerId($custId);
                $entry->setParentId($parentId);
                $entry->setDepth($depth);
                $entry->setPath($path);
                $downline[$custId] = $entry;
            }
        }
        /* update DB data */
        foreach ($downline as $custId => $item) {
            $this->daoCust->updateById($custId, $item);
        }
    }
}