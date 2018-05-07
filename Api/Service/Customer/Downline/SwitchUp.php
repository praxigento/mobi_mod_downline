<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Downline;

use Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp\Response as AResponse;

/**
 * Switch customer's downline to customer's parent (exclude unqualified customer from the game).
 */
interface SwitchUp
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}