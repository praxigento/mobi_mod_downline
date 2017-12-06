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
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    protected $repoCustGroup;

    public function __construct(
        \Praxigento\Downline\Helper\Config $hlpConfig,
        \Praxigento\Downline\Tool\IReferral $hlpReferral,
        \Magento\Customer\Api\GroupRepositoryInterface $repoCustGroup
    )
    {
        $this->hlpConfig = $hlpConfig;
        $this->hlpReferral = $hlpReferral;
        $this->repoCustGroup = $repoCustGroup;
    }

    /**
     * Get referral group data instead of anonymous group.
     *
     * @param \Magento\Customer\Model\GroupManagement $subject
     * @param \Closure $proceed
     * @return \Magento\Customer\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetNotLoggedInGroup(
        \Magento\Customer\Model\GroupManagement $subject,
        \Closure $proceed
    )
    {
        /* call parent to process other around-plugins */
        $result = $proceed();
        /* check referral code in registry */
        $code = $this->hlpReferral->getReferralCode();
        if ($code) {
            /* and return referral group if code exists */
            $groupId = $this->hlpConfig->getReferralsGroupReferrals();
            $result = $this->repoCustGroup->getById($groupId);
        }
        return $result;
    }
}