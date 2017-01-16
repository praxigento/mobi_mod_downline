<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Customer\Model;


class Customer
{
    /** @var \Praxigento\Downline\Helper\Config */
    protected $hlpConfig;
    /** @var \Praxigento\Downline\Tool\IReferral */
    protected $hlpReferral;
    /** @var \Praxigento\Core\Fw\Logger\App */
    protected $logger;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Downline\Helper\Config $hlpConfig,
        \Praxigento\Downline\Tool\IReferral $hlpReferral
    ) {
        $this->logger = $logger;
        $this->hlpConfig = $hlpConfig;
        $this->hlpReferral = $hlpReferral;
    }

    public function aroundGetGroupId(
        \Magento\Customer\Model\Customer $subject,
        \Closure $proceed
    ) {
        $origGroupId = $subject->hasData('group_id');
        if (
            is_null($origGroupId) ||
            $origGroupId == 0
        ) {
            /* check referral code in registry */
            $code = $this->hlpReferral->getReferralCode();
            if ($code) {
                /* and setup referral group if missed */
                $groupId = $this->hlpConfig->getReferralsGroupReferrals();
                $subject->setData('group_id', $groupId);
                $this->logger->info("There is referral code ($code) in the Mage registry. Referral group ($groupId) is used as default for new customer.");
            } else {
                $this->logger->info("There is no group ID for customer and no referral code in the Mage registry.");
            }
        }
        /* call parent to proceed other plugins */
        $result = $proceed();
        return $result;
    }
}