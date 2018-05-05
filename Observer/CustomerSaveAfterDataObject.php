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
    /** @var \Praxigento\Downline\Service\ICustomer */
    private $callCustomer;
    /** @var \Praxigento\Downline\Api\Helper\Referral\CodeGenerator */
    private $hlpCodeGen;
    /** @var bool flag for disabled functionality */
    private static $isDisabled = false;
    /** @var \Magento\Framework\Registry  */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Praxigento\Downline\Api\Helper\Referral\CodeGenerator $hlpCodeGen,
        \Praxigento\Downline\Service\ICustomer $callCustomer
    )
    {
        $this->registry = $registry;
        $this->hlpCodeGen = $hlpCodeGen;
        $this->callCustomer = $callCustomer;
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
                $countryCode = $this->registry->registry(self::A_CUST_COUNTRY);
                $parentId = $this->registry->registry(self::A_PARENT_MAGE_ID);
                $req = new \Praxigento\Downline\Service\Customer\Request\Add();
                $req->setCustomerId($idAfter);
                $req->setParentId($parentId);
                $req->setCountryCode($countryCode);
                if ($mlmId) {
                    $req->setMlmId($mlmId);
                } else {
                    $mlmId = $this->hlpCodeGen->generateMlmId($afterSave);
                    $req->setMlmId($mlmId);
                }
                $refCode = $this->hlpCodeGen->generateReferralCode($afterSave);
                $req->setReferralCode($refCode);
                $this->callCustomer->add($req);
            }
        }
    }
}