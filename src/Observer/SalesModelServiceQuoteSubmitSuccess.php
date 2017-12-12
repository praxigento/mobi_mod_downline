<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Observer;


use Magento\Framework\Event\Observer as Observer;

class SalesModelServiceQuoteSubmitSuccess
    implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Sales\Api\OrderCustomerManagementInterface */
    private $manOrderCust;

    public function __construct(
        \Magento\Sales\Api\OrderCustomerManagementInterface $manOrderCust
    ) {
        $this->manOrderCust = $manOrderCust;
    }

    public function execute(Observer $observer) {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');

        $orderId = $order->getId();
        // $this->manOrderCust->create($orderId);
    }
}