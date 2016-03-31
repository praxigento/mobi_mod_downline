<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Lib\Entity\Snap;
use Praxigento\Downline\Lib\Service\ISnap;

class Call extends \Praxigento\Core\Lib\Service\Base\Call implements ISnap {
    /**
     * Database sub functions for this service.
     *
     * @var Sub\Db
     */
    private $_subDb;
    /**
     * @var Sub\CalcSimple
     */
    private $_subCalc;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Lib\Context\IDbAdapter $dba,
        \Praxigento\Core\Lib\IToolbox $toolbox,
        \Praxigento\Core\Lib\Service\IRepo $callRepo,
        Sub\Db $subDb,
        Sub\CalcSimple $subCalc
    ) {
        parent::__construct($logger, $dba, $toolbox, $callRepo);
        $this->_subDb = $subDb;
        $this->_subCalc = $subCalc;
    }

    /**
     * Calculate downline snapshots up to requested date (including).
     *
     * @param Request\Calc $request
     *
     * @return Response\Calc
     */
    public function calc(Request\Calc $request) {
        $result = new Response\Calc();
        $this->_logger->info("New downline snapshot calculation is requested.");
        $periodTo = $request->getDatestampTo();
        /** @var  $toolPeriod \Praxigento\Core\Lib\Tool\Period */
        $toolPeriod = $this->_toolbox->getPeriod();
        try {
            $this->_getConn()->beginTransaction();
            /* get the last date with calculated snapshots */
            $reqLast = new Request\GetLastDate();
            /** @var  $resp Response\GetLastDate */
            $respLast = $this->getLastDate($reqLast);
            $lastDatestamp = $respLast->getLastDate();
            /* get the snapshot on the last date */
            $snapshot = $this->_subDb->getStateOnDate($lastDatestamp);
            /* get change log for the period */
            $tsFrom = $toolPeriod->getTimestampNextFrom($lastDatestamp);
            $tsTo = $toolPeriod->getTimestampTo($periodTo);
            $changelog = $this->_subDb->getChangesForPeriod($tsFrom, $tsTo);
            /* calculate snapshots for the period */
            $updates = $this->_subCalc->calcSnapshots($snapshot, $changelog);
            /* save new snapshots in DB */
            $this->_subDb->saveCalculatedUpdates($updates);
            $this->_getConn()->commit();
            $result->setAsSucceed();
        } catch(\Exception $e) {
            $this->_getConn()->rollback();
            $this->_logger->error("Cannot add new customer to downline. Exception: " . $e->getMessage());
        }
        return $result;
    }

    /**
     * Calculate the last date for existing downline snap or the "yesterday" for the first change log entry.
     *
     * @param Request\GetLastDate $request
     *
     * @return Response\GetLastDate
     */
    public function getLastDate(Request\GetLastDate $request) {
        $result = new Response\GetLastDate();
        $this->_logger->info("'Get Last Data' operation is requested.");
        /** @var  $toolPeriod \Praxigento\Core\Lib\Tool\Period */
        $toolPeriod = $this->_toolbox->getPeriod();
        /* get the maximal date for existing snapshot */
        $snapMaxDate = $this->_subDb->getSnapMaxDatestamp();
        if($snapMaxDate) {
            /* there is snapshots data */
            $result->setData([ Response\GetLastDate::LAST_DATE => $snapMaxDate ]);
        } else {
            /* there is no snapshot data yet, get change log minimal date  */
            $changelogMinDate = $this->_subDb->getChangelogMinDate();
            if($changelogMinDate) {
                $period = $toolPeriod->getPeriodCurrent($changelogMinDate);
                $dayBefore = $toolPeriod->getPeriodPrev($period);
                $this->_logger->info("The last date for downline snapshot is '$dayBefore'.");
                $result->setData([ Response\GetLastDate::LAST_DATE => $dayBefore ]);
                $result->setAsSucceed();
            }
        }
        $this->_logger->info("'Get Last Data' operation is completed.");
        return $result;
    }

    /**
     * Select downline tree state on the given datestamp.
     *
     * @param Request\GetStateOnDate $request
     *
     * @return Response\GetStateOnDate
     */
    public function getStateOnDate(Request\GetStateOnDate $request) {
        $result = new Response\GetStateOnDate();
        $this->_logger->info("'Get Downline Tree state' operation is requested.");
        $dateOn = $request->getDatestamp();
        $rows = $this->_subDb->getStateOnDate($dateOn);
        $result->setData($rows);
        $result->setAsSucceed();
        $this->_logger->info("'Get Downline Tree state' operation is completed.");
        return $result;
    }

    /**
     * Extend minimal Downline Tree Data (customer & parent) with depth and path.
     *
     * @param Request\ExpandMinimal $request
     *
     * @return Response\ExpandMinimal
     *
     * @deprecated use \Praxigento\Downline\Lib\Tool\ITree::expandMinimal instead
     */
    public function expandMinimal(Request\ExpandMinimal $request) {
        $result = new Response\ExpandMinimal();
        $keyCustomerId = $request->getKeyCustomerId();
        $keyParentId = $request->getKeyParentId();
        $treeIn = $request->getTree();
        /**
         * Validate tree consistency: all parents should be customers too, create map for customers with invalid
         * parents to set this entries as orphans.
         */
        $mapCusts = [ ]; // registry for all customers.
        foreach($treeIn as $ndx => $item) {
            $custId = is_null($keyCustomerId) ? $ndx : $item[$keyCustomerId];
            $mapCusts[] = $custId;
        }
        $mapOrphans = [ ]; // registry for orphan customers.
        foreach($treeIn as $ndx => $item) {
            $custId = is_null($keyCustomerId) ? $ndx : $item[$keyCustomerId];
            $parentId = !is_array($item) ? $item : $item[$keyParentId];
            if(!in_array($parentId, $mapCusts)) {
                $msg = "Parent #$parentId for customer #$custId is not present in the minimal tree.";
                $msg .= " Customer #$custId is set as orphan.";
                $this->_logger->warning($msg);
                $mapOrphans[] = $custId;
            }
        }
        /* create tree (see http://stackoverflow.com/questions/2915748/how-can-i-convert-a-series-of-parent-child-relationships-into-a-hierarchical-tre) */
        $flat = [ ];
        $tree = [ ];
        foreach($treeIn as $ndx => $item) {
            $custId = is_null($keyCustomerId) ? $ndx : $item[$keyCustomerId];
            $parentId = !is_array($item) ? $item : $item[$keyParentId];
            /* filter orphans */
            if(in_array($custId, $mapOrphans)) {
                $parentId = $custId;
            }
            /* map customers into tree */
            if(!isset($flat[$custId])) {
                $flat[$custId] = [ ];
            }
            if($custId != $parentId) {
                $flat[$parentId][$custId] =& $flat[$custId];
            } else {
                /* root node */
                $tree[$custId] =& $flat[$custId];
            }
        }
        /* populate tree with depth/path/... and compose array to insert into DB  */
        $snapData = [ ];
        $this->_composeSnapData($snapData, $tree);
        $result->setSnapData($snapData);
        $result->setAsSucceed();
        return $result;
    }

    /**
     * Compose $result array as array of elements:
     * [
     *  Snap::ATTR_CUSTOMER_ID,
     *  Snap::ATTR_PARENT_ID,
     *  Snap::ATTR_DEPTH,
     *  Snap::ATTR_PATH
     * ]
     * to insert into DB as record in prxgt_dwnl_snap.
     *
     * @param      $result
     * @param      $tree
     * @param null $parentId
     */
    private function _composeSnapData(&$result, $tree, $parentId = null) {
        foreach($tree as $custId => $children) {
            if(is_null($parentId)) {
                /* this is root node */
                $result[$custId] = [
                    Snap::ATTR_CUSTOMER_ID => $custId,
                    Snap::ATTR_PARENT_ID   => $custId,
                    Snap::ATTR_DEPTH       => Cfg::INIT_DEPTH,
                    Snap::ATTR_PATH        => Cfg::DTPS
                ];
            } else {
                $parentData = $result[$parentId];
                $result[$custId] = [
                    Snap::ATTR_CUSTOMER_ID => $custId,
                    Snap::ATTR_PARENT_ID   => $parentId,
                    Snap::ATTR_DEPTH       => $parentData[Snap::ATTR_DEPTH] + 1,
                    Snap::ATTR_PATH        => $parentData[Snap::ATTR_PATH] . $parentId . Cfg::DTPS
                ];
            }
            if(sizeof($children) > 0) {
                $this->_composeSnapData($result, $children, $custId);
            }
        }
    }
}