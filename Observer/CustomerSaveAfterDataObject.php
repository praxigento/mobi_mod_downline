<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Observer;

use Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info as ABlock;

/**
 * Register downline on new customer create event.
 */
class CustomerSaveAfterDataObject
    implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Framework\App\RequestInterface */
    private $appRequest;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
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
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Api\Helper\Referral\CodeGenerator $hlpCodeGen,
        \Praxigento\Downline\Helper\Registry $hlpRegistry,
        \Praxigento\Downline\Api\Service\Customer\Add $servDwnlAdd
    ) {
        $this->registry = $registry;
        $this->appRequest = $appRequest;
        $this->daoDwnlCust = $daoDwnlCust;
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
            $mlmId = $this->getMlmId($afterSave);
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
     * @param \Magento\Customer\Model\Data\Customer $cust
     * @return string
     */
    private function getMlmId($cust)
    {
        $posted = $this->appRequest->getPostValue();
        if (isset($posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_OWN_MLM_ID])) {
            $result = $posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_OWN_MLM_ID];
        } else {
            $result = $this->hlpCodeGen->generateMlmId($cust);
        }
        return $result;
    }

    /**
     * Extract customer's parent ID from posted data or set null to extract it in service later from referral code.
     *
     * @return int|null
     */
    private function getParentId()
    {
        $posted = $this->appRequest->getPostValue();
        if (isset($posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_PARENT_MLM_ID])) {
            $mlmId = $posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_PARENT_MLM_ID];
            $found = $this->daoDwnlCust->getByMlmId($mlmId);
            if ($found) {
                $result = $found->getCustomerId();
            }
        } else {
            $result = null;
        }
        return $result;
    }
}