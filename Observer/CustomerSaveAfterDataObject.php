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
    /** @var \Praxigento\Downline\Api\Helper\Referral\CodeGenerator */
    private $hlpCodeGen;
    /** @var \Praxigento\Downline\Helper\Registry */
    private $hlpRegistry;
    /** @var bool flag for disabled functionality */
    private static $isDisabled = false;
    /** @var \Magento\Framework\Registry  */
    private $registry;
    /** @var \Praxigento\Downline\Api\Service\Customer\Add */
    private $servDwnlAdd;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Praxigento\Downline\Api\Helper\Referral\CodeGenerator $hlpCodeGen,
        \Praxigento\Downline\Helper\Registry $hlpRegistry,
        \Praxigento\Downline\Api\Service\Customer\Add $servDwnlAdd
    )
    {
        $this->registry = $registry;
        $this->hlpCodeGen = $hlpCodeGen;
        $this->hlpRegistry = $hlpRegistry;
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
                $mlmId = $this->hlpCodeGen->generateMlmId($afterSave);
                $countryCode = $this->hlpRegistry->getCustomerCountry();
                $refCode = $this->hlpCodeGen->generateReferralCode($afterSave);
                $req = new \Praxigento\Downline\Api\Service\Customer\Add\Request();
                $req->setCountryCode($countryCode);
                $req->setCustomerId($idAfter);
                $req->setMlmId($mlmId);
                /* parent ID will be extracted from referral codes in service call */
                $req->setParentId(null);
                $req->setReferralCode($refCode);
                $this->servDwnlAdd->exec($req);
            }
        }
    }
}