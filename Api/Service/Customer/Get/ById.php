<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Get;

use Praxigento\Downline\Api\Service\Customer\Get\ById\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Get\ById\Response as AResponse;

interface ById
    extends \Praxigento\Core\Api\Service\Customer\Get\ById
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);
}