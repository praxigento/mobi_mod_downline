<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Helper\Referral;

/**
 * Referral code generator should be overridden in project's application module.
 */
interface CodeGenerator
{
    /**
     * @param \Magento\Customer\Model\Data\Customer|null $data
     * @return string
     */
    public function generate(\Magento\Customer\Model\Data\Customer $data = null);
}