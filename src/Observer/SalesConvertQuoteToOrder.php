<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Observer;

use Magento\Framework\Event\Observer as AObserver;

/**
 * Replace customer group for referrals.
 */
class SalesConvertQuoteToOrder
    implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Praxigento\Downline\Helper\Config */
    private $hlpConfig;
    /** @var \Praxigento\Downline\Tool\IReferral */
    private $hlpReferral;

    public function __construct(
        \Praxigento\Downline\Helper\Config $hlpConfig,
        \Praxigento\Downline\Tool\IReferral $hlpReferral
    )
    {
        $this->hlpConfig = $hlpConfig;
        $this->hlpReferral = $hlpReferral;
    }

    public function execute(AObserver $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();
        $groupId = $order->getCustomerGroupId();
        if ($groupId == \Magento\Customer\Model\GroupManagement::NOT_LOGGED_IN_ID) {
            /* check referral code in registry for guest visitors */
            $code = $this->hlpReferral->getReferralCode();
            if ($code) {
                /* return referral group id if code exists */
                $groupId = $this->hlpConfig->getReferralsGroupReferrals();
                $order->setCustomerGroupId($groupId);
            }
        }
    }
}