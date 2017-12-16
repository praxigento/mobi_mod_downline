<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper\Referral;

/**
 * Set internal ID as referral code by default.
 */
class CodeGenerator
    implements \Praxigento\Downline\Api\Helper\Referral\CodeGenerator
{
    public function generate(\Magento\Customer\Model\Data\Customer $data = null) {
        $result = $data->getId();
        return $result;
    }

}