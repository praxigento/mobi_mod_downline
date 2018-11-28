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
    /** @var \Magento\Framework\App\RequestInterface */
    private $appRequest;
    /** @var \Praxigento\Downline\Api\Helper\Referral\CodeGenerator */
    private $hlpCodeGen;
    /** @var \Praxigento\Downline\Helper\Registry */
    private $hlpRegistry;
    /** @var \Magento\Framework\Registry */
    private $registry;
    /** @var \Praxigento\Downline\Api\Service\Customer\Add */
    private $servDwnlAdd;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $appRequest,
        \Praxigento\Downline\Api\Helper\Referral\CodeGenerator $hlpCodeGen,
        \Praxigento\Downline\Helper\Registry $hlpRegistry,
        \Praxigento\Downline\Api\Service\Customer\Add $servDwnlAdd
    ) {
        $this->registry = $registry;
        $this->appRequest = $appRequest;
        $this->hlpCodeGen = $hlpCodeGen;
        $this->hlpRegistry = $hlpRegistry;
        $this->servDwnlAdd = $servDwnlAdd;
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
            $mlmId = $this->getMlmId($idAfter);
            $parentId = $this->getParentId();
            $countryCode = $this->hlpRegistry->getCustomerCountry();
            $refCode = $this->hlpCodeGen->generateReferralCode($afterSave);
            $req = new \Praxigento\Downline\Api\Service\Customer\Add\Request();
            $req->setCountryCode($countryCode);
            $req->setCustomerId($idAfter);
            $req->setMlmId($mlmId);
            /* parent ID will be extracted from referral codes in service call */
            $req->setParentId($parentId);
            $req->setReferralCode($refCode);
            $this->servDwnlAdd->exec($req);
        }
    }

    /**
     * Extract customer's MLM ID from posted data or generate new one.
     *
     * @param int $custId
     * @return string
     */
    private function getMlmId($custId)
    {
        $posted = $this->appRequest->getPostValue();
        $result = $this->hlpCodeGen->generateMlmId($custId);
        return $result;
    }

    /**
     * Extract customer's parent ID from posted data or set null to extract it in service later.
     *
     * @return int|null
     */
    private function getParentId()
    {
        $result = null;
        return $result;
    }
}