<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Register downline on new customer create event.
 */
class CustomerSaveAfterDataObject implements ObserverInterface
{
    /** @var \Praxigento\Downline\Service\ICustomer */
    protected $callCustomer;

    public function __construct(
        \Praxigento\Downline\Service\ICustomer $callCustomer
    ) {
        $this->callCustomer = $callCustomer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Data\Customer $beforeSave */
        $beforeSave = $observer->getData('orig_customer_data_object');
        /** @var \Magento\Customer\Model\Data\Customer $afterSave */
        $afterSave = $observer->getData('customer_data_object');
        $idBefore = $beforeSave->getId();
        $idAfter = $afterSave->getId();
        if ($idBefore != $idAfter) {
            /* this is newly saved customer, register it into downline */
            $parentId = $beforeSave->{'parentId'};
            $req = new \Praxigento\Downline\Service\Customer\Request\Add();
            $req->setCustomerId($idAfter);
            $req->setParentId($parentId);
            /* TODO: reference should be generated */
            $req->setReference($idAfter);
            $this->callCustomer->add($req);
        }
        return;
    }
}