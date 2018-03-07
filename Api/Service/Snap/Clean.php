<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Snap;

use Praxigento\Downline\Api\Service\Snap\Clean\Request as ARequest;
use Praxigento\Downline\Api\Service\Snap\Clean\Response as AResponse;

/**
 * Clean up all snapshots for downline tree.
 */
interface Clean
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}