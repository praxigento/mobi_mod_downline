<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo;


interface IModule
{
    /**
     * Select MIN date for the existing change log.
     *
     * @return null|string
     */
    public function getChangelogMinDate();

    /**
     * @param $timestampFrom
     * @param $timestampTo
     *
     * @return array
     */
    public function getChangesForPeriod($timestampFrom, $timestampTo);

    /**
     * Select MAX datestamp for downline snapshots.
     *
     * @return null|string YYYYMMDD
     *
     */
    public function getSnapMaxDatestamp();

}