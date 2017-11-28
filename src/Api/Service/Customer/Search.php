<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\Search\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Search\Response as AResponse;

/**
 * Search customers by some criteria (name, email, etc.).
 */
interface Search
    extends \Praxigento\Core\Api\Service\Customer\Search
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}