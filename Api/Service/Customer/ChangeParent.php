<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\ChangeParent\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\ChangeParent\Response as AResponse;

/**
 * Change parent for customer and update all related paths in downline.
 */
interface ChangeParent
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}