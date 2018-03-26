<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Helper;

/**
 * Helper to get configuration parameters related to the module.
 */
class Config
    implements \Praxigento\Downline\Api\Helper\Config
{

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getReferralsGroupReferrals()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/group_referrals');
        $result = filter_var($result, FILTER_VALIDATE_INT);
        return $result;
    }

    public function getReferralsGroupReferralsRegistered()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/group_referrals_registered');
        $result = filter_var($result, FILTER_VALIDATE_INT);
        return $result;
    }

    public function getReferralsRootAnonymous()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/root_anonymous');
        return $result;
    }

}