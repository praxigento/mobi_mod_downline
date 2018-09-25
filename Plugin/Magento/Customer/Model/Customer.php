<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Magento\Customer\Model;

class Customer
{
    /** @var \Praxigento\Downline\Helper\Config */
    private $hlpConfig;
    /** @var \Praxigento\Downline\Api\Helper\Referral */
    private $hlpReferral;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Helper\Config $hlpConfig,
        \Praxigento\Downline\Api\Helper\Referral $hlpReferral
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
            $origGroupId == \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID
        ) {
            /* check referral code in registry */
            $code = $this->hlpReferral->getReferralCode();
            if ($code) {
                /* and setup default group for registered retails if missed */
                $groupId = $this->hlpConfig->getReferralsGroupReferralsRegistered();
                $subject->setData('group_id', $groupId);
                $this->logger->info("There is referral code ($code) in the Mage registry. Referral group ($groupId) is used as default for new customer.");
            }
        }
        /* call parent to proceed other plugins */
        $result = $proceed();
        return $result;
    }
}