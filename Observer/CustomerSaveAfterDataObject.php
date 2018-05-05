<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Observer;

/**
 * Register downline on new customer create event.
 */
class CustomerSaveAfterDataObject
    implements \Magento\Framework\Event\ObserverInterface
{
    const A_CUST_COUNTRY = 'prxgtCustCountry';
    const A_CUST_MLM_ID = 'prxgtCustMlmId';
    const A_PARENT_MAGE_ID = 'prxgtParentMageId';

    /** @var \Praxigento\Downline\Api\Helper\Referral\CodeGenerator */
    private $hlpCodeGen;
    /** @var bool flag for disabled functionality */
    private static $isDisabled = false;
    /** @var \Magento\Framework\Registry  */
    private $registry;
    /** @var \Praxigento\Downline\Api\Service\Customer\Add */
    private $servDwnlAdd;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Praxigento\Downline\Api\Helper\Referral\CodeGenerator $hlpCodeGen,
        \Praxigento\Downline\Api\Service\Customer\Add $servDwnlAdd
    )
    {
        $this->registry = $registry;
        $this->hlpCodeGen = $hlpCodeGen;
        $this->servDwnlAdd = $servDwnlAdd;
    }

    /**
     * Disable this observer functionality (on data migration).
     */
    public static function disable()
    {
        self::$isDisabled = true;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* downline processing in observer is disabled in data migration */
        if (!self::$isDisabled) {
            /** @var \Magento\Customer\Model\Data\Customer $beforeSave */
            $beforeSave = $observer->getData('orig_customer_data_object');
            /** @var \Magento\Customer\Model\Data\Customer $afterSave */
            $afterSave = $observer->getData('customer_data_object');
            $idBefore = $beforeSave && $beforeSave->getId() ?? null;
            $idAfter = $afterSave->getId();
            if ($idBefore != $idAfter) {
                /* this is newly saved customer, register it into downline */
                /* get MLM ID for replicated client (if exists) */
                $mlmId = $this->registry->registry(self::A_CUST_MLM_ID);
                if (!$mlmId) {
                    $mlmId = $this->hlpCodeGen->generateMlmId($afterSave);
                }
                $parentId = $this->registry->registry(self::A_PARENT_MAGE_ID);
                $countryCode = $this->registry->registry(self::A_CUST_COUNTRY);
                $refCode = $this->hlpCodeGen->generateReferralCode($afterSave);
                $req = new \Praxigento\Downline\Api\Service\Customer\Add\Request();
                $req->setCountryCode($countryCode);
                $req->setCustomerId($idAfter);
                $req->setMlmId($mlmId);
                $req->setParentId($parentId);
                $req->setReferralCode($refCode);
                $this->servDwnlAdd->exec($req);
            }
        }
    }
}