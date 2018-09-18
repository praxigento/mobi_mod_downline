<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Api\Service\Snap;

use Praxigento\Downline\Api\Service\Snap\Calc\Request as ARequest;
use Praxigento\Downline\Api\Service\Snap\Calc\Response as AResponse;

/**
 * Calculate downline snapshots up to requested date (including).
 */
interface Calc
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}