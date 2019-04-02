<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Observer\CustomerSaveAfterDataObject\A;

use Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info as ABlock;
use Praxigento\Downline\Repo\Data\Change\Group as EChangeGroup;

class SaveNew
{
    /** @var \Magento\Framework\App\RequestInterface */
    private $appRequest;
    /** @var \Praxigento\Downline\Repo\Dao\Change\Group */
    private $daoChangeGroup;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Api\Helper\Referral\CodeGenerator */
    private $hlpCodeGen;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Praxigento\Downline\Helper\Registry */
    private $hlpRegistry;
    /** @var \Praxigento\Downline\Api\Service\Customer\Add */
    private $servDwnlAdd;

    public function __construct(
        \Magento\Framework\App\RequestInterface $appRequest,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Repo\Dao\Change\Group $daoChangeGroup,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Downline\Api\Helper\Referral\CodeGenerator $hlpCodeGen,
        \Praxigento\Downline\Helper\Registry $hlpRegistry,
        \Praxigento\Downline\Api\Service\Customer\Add $servDwnlAdd
    ) {
        $this->appRequest = $appRequest;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoChangeGroup = $daoChangeGroup;
        $this->hlpDate = $hlpDate;
        $this->hlpCodeGen = $hlpCodeGen;
        $this->hlpRegistry = $hlpRegistry;
        $this->servDwnlAdd = $servDwnlAdd;
    }

    /**
     * @param \Magento\Customer\Model\Data\Customer $saved
     * @throws \Exception
     */
    public function exec($saved)
    {
        $this->registerCustomer($saved);
        $this->registerGroup($saved);
    }

    /**
     * Extract code for customer's registration country from posted data.
     *
     * @return string
     */
    private function getCountryCode()
    {
        $posted = $this->appRequest->getPostValue();
        if (isset($posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_COUNTRY_CODE])) {
            $result = $posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_COUNTRY_CODE];
        } else {
            $result = $this->hlpRegistry->getCustomerCountry();
        }
        return $result;
    }

    /**
     * Extract customer's MLM ID from posted data or generate new one.
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return string
     */
    private function getMlmId($customer)
    {
        $posted = $this->appRequest->getPostValue();
        if (
            isset($posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_OWN_MLM_ID]) &&
            !empty($posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_OWN_MLM_ID])
        ) {
            $result = $posted['customer'][ABlock::TMPL_FLDGRP][ABlock::TMPL_FIELD_OWN_MLM_ID];
        } else {
            $result = $this->hlpCodeGen->generateMlmId($customer);
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
                $result = $found->getCustomerRef();
            }
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @throws \Exception
     */
    private function registerCustomer($customer)
    {
        $custId = $customer->getId();
        $mlmId = $this->getMlmId($customer);
        $parentId = $this->getParentId();
        $countryCode = $this->getCountryCode();
        $refCode = $this->hlpCodeGen->generateReferralCode($customer);
        $req = new \Praxigento\Downline\Api\Service\Customer\Add\Request();
        $req->setCountryCode($countryCode);
        $req->setCustomerId($custId);
        $req->setMlmId($mlmId);
        /* parent ID will be extracted from referral codes in service call */
        $req->setParentId($parentId);
        $req->setReferralCode($refCode);
        $this->servDwnlAdd->exec($req);
    }

    /**
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @throws \Exception
     */
    private function registerGroup($customer)
    {
        $custId = $customer->getId();
        $groupId = $customer->getGroupId();
        $data = new EChangeGroup();
        $data->setCustomerRef($custId);
        $data->setGroupOld($groupId);
        $data->setGroupNew($groupId);
        $dateChanged = $this->hlpDate->getUtcNowForDb();
        $data->setDateChanged($dateChanged);
        $this->daoChangeGroup->create($data);
    }
}