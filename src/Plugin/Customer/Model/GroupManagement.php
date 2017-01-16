<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Customer\Model;


class GroupManagement
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

    /**
     * TODO: complete it or remove it.
     *
     * @param \Magento\Customer\Model\Customer $subject
     * @param \Closure $proceed
     * @param null $storeId
     * @return mixed
     */
    public function aroundGetDefaultGroup(
        \Magento\Customer\Model\Customer $subject,
        \Closure $proceed,
        $storeId = null
    ) {
        /* call parent to get default group */
        $result = $proceed($storeId);
//        if($result==) {}
//        if (!$subject->hasData('group_id')) {
//            /* check referral code in registry */
//            $code = $this->hlpReferral->getReferralCode();
//            if ($code) {
//                /* and setup referral group if missed */
//                $groupId = $this->hlpConfig->getReferralsGroupReferrals();
//                $subject->setData('group_id', $groupId);
//            }
//        }
        return $result;
    }
}