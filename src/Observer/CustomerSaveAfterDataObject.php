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
    const A_CUST_MLM_ID = 'prxgtCustMlmId';
    const A_PARENT_MAGE_ID = 'prxgtParentMageId';

    /** @var \Praxigento\Downline\Service\ICustomer */
    private $callCustomer;

    public function __construct(
        \Praxigento\Downline\Service\ICustomer $callCustomer
    )
    {
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
            $mlmId = $beforeSave->{self::A_CUST_MLM_ID} ?? null;
            $parentId = $beforeSave->{self::A_PARENT_MAGE_ID} ?? null;
            $req = new \Praxigento\Downline\Service\Customer\Request\Add();
            $req->setCustomerId($idAfter);
            $req->setParentId($parentId);
            if ($mlmId) {
                $req->setReference($mlmId);
            } else {
                /* TODO: reference should be generated */
                $req->setReference($idAfter);
            }
            $this->callCustomer->add($req);
        }
        return;
    }
}