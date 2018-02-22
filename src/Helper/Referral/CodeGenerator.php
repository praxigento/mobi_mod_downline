<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper\Referral;

/**
 * Default codes generator for downline customers (set internal ID as MLM ID & referral code).
 */
class CodeGenerator
    implements \Praxigento\Downline\Api\Helper\Referral\CodeGenerator
{
    public function generateMlmId(\Magento\Customer\Model\Data\Customer $data)
    {
        $result = $data->getId();
        return $result;
    }

    public function generateReferralCode(\Magento\Customer\Model\Data\Customer $data)
    {
        $result = $data->getId();
        return $result;
    }
}