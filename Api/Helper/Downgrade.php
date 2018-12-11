<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Helper;

/**
 * Helper for customers downgrade functionality (transitions from distributors to regular customers).
 */
interface Downgrade
{

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $custMage
     * @param \Praxigento\Downline\Repo\Data\Customer $custDwnl
     * @return bool
     */
    public function canDowngrade($custMage = null, $custDwnl = null);
}