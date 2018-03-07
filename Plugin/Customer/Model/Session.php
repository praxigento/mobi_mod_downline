<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Customer\Model;


class Session
{
    /** @var \Praxigento\Downline\Helper\Config */
    private $hlpConfig;
    /** @var \Praxigento\Downline\Api\Helper\Referral */
    private $hlpReferral;

    public function __construct(
        \Praxigento\Downline\Helper\Config $hlpConfig,
        \Praxigento\Downline\Api\Helper\Referral $hlpReferral
    )
    {
        $this->hlpConfig = $hlpConfig;
        $this->hlpReferral = $hlpReferral;
    }

    public function aroundGetCustomerGroupId(
        \Magento\Customer\Model\Session $subject,
        \Closure $proceed
    )
    {
        /* call parent to get group ID and to process other around-plugins */
        $result = $proceed();
        if ($result == \Magento\Customer\Model\GroupManagement::NOT_LOGGED_IN_ID) {
            /* check referral code in registry for guest visitors */
            $code = $this->hlpReferral->getReferralCode();
            if ($code) {
                /* return referral group id if code exists */
                $result = $this->hlpConfig->getReferralsGroupReferrals();
            }
        }
        return $result;
    }
}