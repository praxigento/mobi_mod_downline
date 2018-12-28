<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Observer;

/**
 * Register downline on new customer create event or re-build downline on customer group change.
 */
class CustomerSaveAfterDataObject
    implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Praxigento\Downline\Observer\CustomerSaveAfterDataObject\A\GroupSwitch */
    private $aGroupSwitch;
    /** @var \Praxigento\Downline\Observer\CustomerSaveAfterDataObject\A\SaveNew */
    private $aSaveNew;

    public function __construct(
        \Praxigento\Downline\Observer\CustomerSaveAfterDataObject\A\GroupSwitch $aGroupSwitch,
        \Praxigento\Downline\Observer\CustomerSaveAfterDataObject\A\SaveNew $aSaveNew
    ) {
        $this->aGroupSwitch = $aGroupSwitch;
        $this->aSaveNew = $aSaveNew;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Data\Customer $beforeSave */
        $beforeSave = $observer->getData('orig_customer_data_object');
        /** @var \Magento\Customer\Model\Data\Customer $afterSave */
        $afterSave = $observer->getData('customer_data_object');
        $idBefore = $beforeSave && $beforeSave->getId() ?? null;
        $idAfter = $afterSave->getId();
        if ($idBefore != $idAfter) {
            /* this is newly saved customer, register it into downline */
            $this->aSaveNew->exec($afterSave);
        } else {
            /* this is customer update */
            $groupIdBefore = $beforeSave->getGroupId();
            $this->aGroupSwitch->exec($groupIdBefore, $afterSave);
        }
    }

}