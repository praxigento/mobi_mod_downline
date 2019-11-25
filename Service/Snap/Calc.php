<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Service\Snap;


use Praxigento\Downline\Api\Service\Snap\Calc\Request as ARequest;
use Praxigento\Downline\Api\Service\Snap\Calc\Response as AResponse;
use Praxigento\Downline\Api\Service\Snap\GetLastDate\Request as AGetLastDateRequest;
use Praxigento\Downline\Api\Service\Snap\GetLastDate\Response as AGetLastDateResponse;
use Praxigento\Downline\Repo\Data\Change as EChange;
use Praxigento\Downline\Repo\Data\Snap as ESnap;
use Praxigento\Downline\Repo\Query\Snap\OnDate\Builder as QSnapOnDate;
use Praxigento\Downline\Service\Snap\Calc\A\Repo\Query\GetChanges as QGetChanges;

class Calc
    implements \Praxigento\Downline\Api\Service\Snap\Calc
{
    /** @var \Praxigento\Downline\Service\Snap\Calc\A\ComposeUpdates */
    private $aComposeUpdates;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Repo\Dao\Snap */
    private $daoSnap;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Downline\Service\Snap\Calc\A\Repo\Query\GetChanges */
    private $qGetChanges;
    /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder */
    private $qSnapOnDate;
    /** @var \Praxigento\Downline\Api\Service\Snap\GetLastDate */
    private $servGetLastDate;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Repo\Dao\Snap $daoSnap,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder $qSnapOnDate,
        \Praxigento\Downline\Api\Service\Snap\GetLastDate $servGetLastDate,
        \Praxigento\Downline\Service\Snap\Calc\A\Repo\Query\GetChanges $qGetChanges,
        \Praxigento\Downline\Service\Snap\Calc\A\ComposeUpdates $aComposeUpdates
    ) {
        $this->logger = $logger;
        $this->hlpPeriod = $hlpPeriod;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoSnap = $daoSnap;
        $this->qSnapOnDate = $qSnapOnDate;
        $this->servGetLastDate = $servGetLastDate;
        $this->qGetChanges = $qGetChanges;
        $this->aComposeUpdates = $aComposeUpdates;
    }

    private function cleanLastDate($ds)
    {
        $where = ESnap::A_DATE . '>=' . $ds;
        $this->daoSnap->delete($where);
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $this->logger->info("New downline snapshot calculation is requested.");
        /* get the last date with calculated snapshots */
        $dsLast = $this->getLastDate();
        /* clean snapshots on the last date (MOBI-956 - data can be incomplete) */
        $this->cleanLastDate($dsLast);
        /* get the snapshot on the last date */
        $snapshot = $this->getDwnlSnap($dsLast);
        /* get change log for the period starting from the last date */
        $changelog = $this->getDwnlLog($dsLast);
        /* calculate snapshots for the period */
        [$updates, $current] = $this->aComposeUpdates->exec($snapshot, $changelog);
        /* save new snapshots in DB */
        $this->daoSnap->saveCalculatedUpdates($updates);
        $this->updateCurrentState($current);
        /* finalize processing */
        return $result;
    }

    /**
     * Load change log starting from the given date.
     *
     * @param string $datestamp
     * @return \Praxigento\Downline\Repo\Data\Change[]
     * @throws \Exception
     */
    private function getDwnlLog($datestamp)
    {
        $result = [];
        /* prepare query parameters */
        $tsFrom = $this->hlpPeriod->getTimestampFrom($datestamp);
        $periodTo = $this->hlpPeriod->getPeriodCurrent();
        $tsTo = $this->hlpPeriod->getTimestampTo($periodTo);
        /* perform query */
        $query = $this->qGetChanges->build();
        $conn = $query->getConnection();
        $bind = [
            QGetChanges::BND_DATE_FROM => $tsFrom,
            QGetChanges::BND_DATE_TO => $tsTo
        ];
        $rs = $conn->fetchAll($query, $bind);
        /* compose result */
        foreach ($rs as $one) {
            $item = new EChange($one);
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Load downline snapshot on the given date.
     *
     * @param string $datestamp
     * @return ESnap[]
     * @throws \Exception
     */
    private function getDwnlSnap($datestamp)
    {
        $result = [];
        $query = $this->qSnapOnDate->build();
        $query->order(QSnapOnDate::AS_DWNL_SNAP . '.' . ESnap::A_DEPTH);
        $conn = $query->getConnection();
        $bind = [
            QSnapOnDate::BND_ON_DATE => $datestamp
        ];
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $one) {
            $item = new ESnap($one);
            $custId = $item->getCustomerRef();
            $result[$custId] = $item;
        }
        return $result;
    }

    /**
     * Get datestamp (YYYYMMDD) for the last date with downline snapshots data.
     * @return string
     * @throws \Exception
     */
    private function getLastDate()
    {
        /* get the last date with calculated snapshots */
        $req = new AGetLastDateRequest();
        /** @var  AGetLastDateResponse $resp */
        $resp = $this->servGetLastDate->exec($req);
        $result = $resp->getLastDate();
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Repo\Data\Snap[] $current
     */
    private function updateCurrentState($current)
    {
        /** @var \Praxigento\Downline\Repo\Data\Customer[] $all */
        $all = $this->daoDwnlCust->get();
        $indexed = [];
        /** @var \Praxigento\Downline\Repo\Data\Customer $cust */
        foreach ($all as $cust) {
            $custId = $cust->getCustomerRef();
            $indexed[$custId] = $cust;
        }
        /** @var \Praxigento\Downline\Repo\Data\Snap $snap */
        foreach ($current as $snap) {
            $custId = $snap->getCustomerRef();
            $parentId = $snap->getParentRef();
            $path = $snap->getPath();
            $depth = $snap->getDepth();
            /** @var \Praxigento\Downline\Repo\Data\Customer $cust */
            $cust = $indexed[$custId];
            $savedParentId = $cust->getParentRef();
            $savedPath = $cust->getPath();
            $savedDepth = $cust->getDepth();
            if (
                ($parentId != $savedParentId) ||
                ($path != $savedPath) ||
                ($depth != $savedDepth)
            ) {
                $this->logger->warning("Update wrong downline data for customer #$custId. "
                    . "Data (parent/depth/path): $parentId/$savedParentId; $depth/$savedDepth; $path/$savedPath.");
                $cust->setParentRef($parentId);
                $cust->setPath($path);
                $cust->setDepth($depth);
                $this->daoDwnlCust->updateById($custId, $cust);
            }
        }
    }

}