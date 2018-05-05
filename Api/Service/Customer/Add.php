<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\Add\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Add\Response as AResponse;

/**
 * Add customer to downline.
 */
interface Add
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}