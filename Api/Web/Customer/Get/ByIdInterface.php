<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Customer\Get;

/**
 * Get customer data by ID.
 */
interface ByIdInterface
    extends \Praxigento\Core\Api\Web\Customer\Get\ByIdInterface
{
    /**
     * @param \Praxigento\Downline\Api\Web\Customer\Get\ById\Request $request
     * @return \Praxigento\Downline\Api\Web\Customer\Get\ById\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}