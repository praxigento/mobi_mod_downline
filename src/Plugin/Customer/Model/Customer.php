<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Customer\Model;


class Customer
{
    /** @var \Praxigento\Downline\Helper\Config  */
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

    public function aroundGetGroupId(
        \Magento\Customer\Model\Customer $subject,
        \Closure $proceed
    ) {
        if (!$subject->hasData('group_id')) {
            /* check referral code in registry */
            $code = $this->hlpReferral->getReferralCode();
            if ($code) {
                /* and setup referral group if missed */
                $groupId = $this->hlpConfig->getReferralsGroupReferrals();
                $subject->setData('group_id', $groupId);
            }
        }
        /* call parent to proceed other plugins */
        $result = $proceed();
        return $result;
    }
}