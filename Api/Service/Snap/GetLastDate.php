<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Api\Service\Snap;

use Praxigento\Downline\Api\Service\Snap\GetLastDate\Request as ARequest;
use Praxigento\Downline\Api\Service\Snap\GetLastDate\Response as AResponse;

/**
 * Calculate the last date for existing downline snap or the "yesterday" for the first change log entry.
 */
interface GetLastDate
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}