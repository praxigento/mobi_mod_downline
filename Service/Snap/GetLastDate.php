<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Service\Snap;


use Praxigento\Downline\Api\Service\Snap\GetLastDate\Request as ARequest;
use Praxigento\Downline\Api\Service\Snap\GetLastDate\Response as AResponse;

/**
 * Calculate the last date for existing downline snap or the "yesterday" for the first change log entry.
 */
class GetLastDate
    implements \Praxigento\Downline\Api\Service\Snap\GetLastDate
{
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    private $daoChange;
    /** @var \Praxigento\Downline\Repo\Dao\Snap */
    private $daoSnap;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Repo\Dao\Change $daoChange,
        \Praxigento\Downline\Repo\Dao\Snap $daoSnap,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod
    ) {
        $this->logger = $logger;
        $this->daoChange = $daoChange;
        $this->daoSnap = $daoSnap;
        $this->hlpPeriod = $hlpPeriod;
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);

        /* get the maximal date for existing snapshot */
        $snapMaxDate = $this->daoSnap->getMaxDatestamp();
        if ($snapMaxDate) {
            /* there is snapshot data */
            $lastDate = $snapMaxDate;
            $this->logger->info("The last date for existing downline snapshot is '$lastDate'.");
        } else {
            /* there is no snapshot data yet, get change log minimal date  */
            $changelogMinDate = $this->daoChange->getChangelogMinDate();
            if ($changelogMinDate) {
                $period = $this->hlpPeriod->getPeriodCurrent($changelogMinDate);
                $lastDate = $this->hlpPeriod->getPeriodPrev($period);
                $this->logger->info("There is no downline snapshots. The last date is '$lastDate' (1 day before the first downline log record).");
            }
        }

        /* finalize processing */
        $result = new AResponse();
        $result->setLastDate($lastDate);
        return $result;
    }
}