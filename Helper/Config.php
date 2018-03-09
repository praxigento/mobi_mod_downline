<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Helper;

/**
 * Helper to get configuration parameters related to the module.
 */
class Config
{

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Group ID for referral customers.
     * @return int
     *
     */
    public function getReferralsGroupReferrals()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/group_referrals');
        $result = filter_var($result, FILTER_VALIDATE_INT);
        return $result;
    }

    /**
     * ID for signup group of the referral customers.
     * @return int
     *
     */
    public function getReferralsGroupReferralsRegistered()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/group_referrals_registered');
        $result = filter_var($result, FILTER_VALIDATE_INT);
        return $result;
    }

    /**
     * MLM ID for root customer to be parent for all anonymous.
     * @return string|false return 'false' if option is not set.
     */
    public function getReferralsRootAnonymous()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/root_anonymous');
        return $result;
    }

}