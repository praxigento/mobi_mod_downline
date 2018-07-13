<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Parent;

use Praxigento\Downline\Api\Service\Customer\Parent\Change\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Parent\Change\Response as AResponse;

/**
 * Change parent for customer and update all related paths in downline.
 */
interface Change
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}