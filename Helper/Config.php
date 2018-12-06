<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper;

use Magento\Store\Model\ScopeInterface as AScope;

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

    public function getDowngradeGroupsDistrs()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/downgrade/groups_distrs', AScope::SCOPE_STORE);
        $result = explode(',', $result);
        return $result;
    }

    public function getDowngradeGroupUnqual()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/downgrade/group_unqual', AScope::SCOPE_STORE);
        $result = filter_var($result, FILTER_VALIDATE_INT);
        return $result;
    }

    public function getReferralsGroupReferrals()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/group_referrals', AScope::SCOPE_STORE);
        $result = filter_var($result, FILTER_VALIDATE_INT);
        return $result;
    }

    public function getReferralsGroupReferralsRegistered()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/group_referrals_registered', AScope::SCOPE_STORE);
        $result = filter_var($result, FILTER_VALIDATE_INT);
        return $result;
    }

    public function getReferralsRootAnonymous()
    {
        $result = $this->scopeConfig->getValue('praxigento_downline/referrals/root_anonymous', AScope::SCOPE_STORE);
        return $result;
    }
}