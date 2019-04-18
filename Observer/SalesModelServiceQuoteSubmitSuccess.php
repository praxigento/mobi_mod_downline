<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Observer;


use Magento\Framework\Event\Observer as Observer;

class SalesModelServiceQuoteSubmitSuccess
    implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Praxigento\Downline\Helper\Registry */
    private $hlpRegistry;
    /** @var \Magento\Sales\Api\OrderCustomerManagementInterface */
    private $manOrderCust;

    public function __construct(
        \Magento\Sales\Api\OrderCustomerManagementInterface $manOrderCust,
        \Praxigento\Downline\Helper\Registry $hlpRegistry
    ) {
        $this->manOrderCust = $manOrderCust;
        $this->hlpRegistry = $hlpRegistry;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $sale */
        $sale = $observer->getData('order');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');
        $isGuest = $quote->getCustomerIsGuest();
        if ($isGuest) {
            $saleId = $sale->getId();
            $quoteId = $quote->getId();
            $this->hlpRegistry->putQuoteId($quoteId);
            $this->manOrderCust->create($saleId);
        }
    }
}