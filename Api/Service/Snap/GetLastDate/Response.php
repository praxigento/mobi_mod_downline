<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Api\Service\Snap\GetLastDate;

/**
 * Calculate the last date for existing downline snap or the "yesterday" for the first change log entry.
 */
class Response
    extends \Praxigento\Core\Data
{
    const LAST_DATE = 'last_date';

    public function getLastDate()
    {
        $result = $this->get(self::LAST_DATE);
        return $result;
    }

    public function setLastDate($data)
    {
        $this->set(self::LAST_DATE, $data);
    }
}