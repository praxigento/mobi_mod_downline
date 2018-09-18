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
use Praxigento\Downline\Repo\Data\Snap as ESnap;

class Calc
    implements \Praxigento\Downline\Api\Service\Snap\Calc
{
    /** @var \Praxigento\Downline\Service\Snap\Calc\A\ComposeUpdates */
    private $aComposeUpdates;
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    private $daoChange;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoCust;
    /** @var \Praxigento\Downline\Repo\Dao\Snap */
    private $daoSnap;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Downline\Api\Service\Snap\GetLastDate */
    private $servGetLastDate;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Downline\Repo\Dao\Change $daoChange,
        \Praxigento\Downline\Repo\Dao\Customer $daoCust,
        \Praxigento\Downline\Repo\Dao\Snap $daoSnap,
        \Praxigento\Downline\Api\Service\Snap\GetLastDate $servGetLastDate,
        \Praxigento\Downline\Service\Snap\Calc\A\ComposeUpdates $aComposeUpdates
    ) {
        $this->logger = $logger;
        $this->hlpPeriod = $hlpPeriod;
        $this->daoChange = $daoChange;
        $this->daoCust = $daoCust;
        $this->daoSnap = $daoSnap;
        $this->servGetLastDate = $servGetLastDate;
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
        $updates = $this->aComposeUpdates->exec($snapshot, $changelog);
        /* save new snapshots in DB */
        $this->daoSnap->saveCalculatedUpdates($updates);
        /* finalize processing */
        return $result;
    }

    /**
     * Load change log starting from the given date.
     *
     * @param string $datestamp
     * @return \Praxigento\Downline\Repo\Data\Change[]
     */
    private function getDwnlLog($datestamp)
    {
        $tsFrom = $this->hlpPeriod->getTimestampFrom($datestamp);
        $periodTo = $this->hlpPeriod->getPeriodCurrent();
        $tsTo = $this->hlpPeriod->getTimestampTo($periodTo);
        $result = $this->daoChange->getChangesForPeriod($tsFrom, $tsTo);
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
        $snapshot = $this->daoSnap->getStateOnDate($datestamp);
        foreach ($snapshot as $one) {
            $item = new ESnap($one);
            $custId = $item->getCustomerId();
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

}