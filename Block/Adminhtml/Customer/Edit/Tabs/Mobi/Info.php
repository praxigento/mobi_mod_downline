<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi;

use Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info\A\Repo\Query\Load as QLoad;

/**
 * @see ./view/adminhtml/templates/customer/edit/tabs/info.phtml
 */
class Info
    extends \Magento\Backend\Block\Template
{
    /**
     * See input nodes names in './view/adminhtml/templates/customer/edit/tabs/info.phtml'
     */
    const TMPL_FIELD_COUNTRY_CODE = 'country_code';
    const TMPL_FIELD_OWN_MLM_ID = 'own_mlm_id';
    const TMPL_FIELD_PARENT_MLM_ID = 'parent_mlm_id';
    const TMPL_FLDGRP = 'mobi_dwnl';
    /**
     * Available countries for customer residence in downline.
     *
     * @var array
     */
    private $cacheCountries;
    /**
     * Customer data being loaded from DB.
     *
     * @var array
     */
    private $cacheCustData;
    /** @var \Magento\Directory\Model\CountryFactory */
    private $factCountry;
    /** @var \Magento\Directory\Model\AllowedCountries */
    private $modAllowedCountries;
    /** @var \Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info\A\Repo\Query\Load */
    private $qLoad;
    /** @var \Magento\Framework\Registry */
    private $registry;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCustomer;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCustomer,
        \Magento\Directory\Model\CountryFactory $factCountry,
        \Magento\Directory\Model\AllowedCountries $modAllowedCountries,
        \Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info\A\Repo\Query\Load $qLoad,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->repoCustomer = $repoCustomer;
        $this->factCountry = $factCountry;
        $this->modAllowedCountries = $modAllowedCountries;
        $this->qLoad = $qLoad;
    }

    protected function _beforeToHtml()
    {
        $this->loadCustomerData();
        $this->loadCountries();
        return parent::_beforeToHtml();;
    }

    public function getCountriesAvailable()
    {
        return $this->cacheCountries;
    }

    public function getCountryCode()
    {
        $result = $this->cacheCustData[QLoad::A_COUNTRY_CODE] ?? '';
        return $result;
    }

    public function getMlmId()
    {
        $result = $this->cacheCustData[QLoad::A_MLM_ID] ?? '';
        return $result;
    }

    public function getParentCustId()
    {
        $result = $this->cacheCustData[QLoad::A_PARENT_CUST_ID] ?? '';
        return $result;
    }

    public function getParentEmail()
    {
        $result = $this->cacheCustData[QLoad::A_PARENT_EMAIL] ?? '';
        return $result;
    }

    public function getParentMlmId()
    {
        $result = $this->cacheCustData[QLoad::A_PARENT_MLM_ID] ?? '';
        return $result;
    }

    public function getParentName()
    {
        $first = $this->cacheCustData[QLoad::A_PARENT_FIRST] ?? '';
        $last = $this->cacheCustData[QLoad::A_PARENT_LAST] ?? '';
        $result = trim("$first $last");
        return $result;
    }

    public function getParentViewUrl()
    {
        $custId = $this->getParentCustId();
        $result = $this->getUrl('customer/index/edit', ['id' => $custId]);
        return $result;
    }

    public function hasParent()
    {
        $result = !empty($this->cacheCustData[QLoad::A_PARENT_MLM_ID]);
        return $result;
    }

    /**
     * Load available countries and compose [$code=>$name] array.
     */
    private function loadCountries()
    {
        $items = [];
        $country = $this->factCountry->create();
        $found = $this->modAllowedCountries->getAllowedCountries();
        foreach ($found as $code) {
            $country->loadByCode($code);
            $name = $country->getName();
            $items[$code] = $name;
        }
        $this->cacheCountries = $items;
    }

    /**
     * Load block's working data before rendering.
     */
    private function loadCustomerData()
    {
        $custId = $this->registry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
        if ($custId) {
            $query = $this->qLoad->build();
            $conn = $query->getConnection();
            $bind = [
                QLoad::BND_CUST_ID => $custId
            ];
            $rs = $conn->fetchAll($query, $bind);
            $this->cacheCustData = reset($rs);
        }
    }
}