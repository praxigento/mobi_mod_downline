<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Customer\Model;


class Session
{
    /** @var \Praxigento\Downline\Helper\Config */
    protected $hlpConfig;
    /** @var \Praxigento\Downline\Tool\IReferral */
    protected $hlpReferral;

    public function __construct(
        \Praxigento\Downline\Helper\Config $hlpConfig,
        \Praxigento\Downline\Tool\IReferral $hlpReferral
    ) {
        $this->hlpConfig = $hlpConfig;
        $this->hlpReferral = $hlpReferral;
    }

    public function aroundGetCustomerGroupId(
        \Magento\Customer\Model\Session $subject,
        \Closure $proceed
    ) {
        /* call parent to get group ID and to process other around-plugins */
        $result = $proceed();
        /* check referral code in registry */
        $code = $this->hlpReferral->getReferralCode();
        if ($code) {
            /* and return referral group id if code exists */
            $result = $this->hlpConfig->getReferralsGroupReferrals();
        }
        return $result;
    }
}