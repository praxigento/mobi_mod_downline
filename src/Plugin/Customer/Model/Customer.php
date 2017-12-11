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
    /** @var \Praxigento\Core\App\Logger\App */
    protected $logger;

    public function __construct(
        \Praxigento\Core\App\Logger\App $logger,
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