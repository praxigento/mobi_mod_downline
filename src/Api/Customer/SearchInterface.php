<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Customer;

/**
 * Get suggestions for customers by key (name/email/mlm_id).
 */
interface SearchInterface
{
    /**
     * @param \Praxigento\Downline\Api\Customer\Search\Request $req
     * @return \Praxigento\Downline\Api\Customer\Search\Response
     */
    public function exec(\Praxigento\Downline\Api\Customer\Search\Request $req);
}