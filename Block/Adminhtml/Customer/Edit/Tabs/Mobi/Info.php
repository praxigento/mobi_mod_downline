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
    const TMPL_FIELD_OWN_MLM_ID = 'own_mlm_id';
    const TMPL_FIELD_PARENT_MLM_ID = 'parent_mlm_id';
    const TMPL_FLDGRP = 'mobi_dwnl';

    /**
     * Data being loaded from DB.
     *
     * @var array
     */
    private $cacheData;

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
        \Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info\A\Repo\Query\Load $qLoad,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->repoCustomer = $repoCustomer;
        $this->qLoad = $qLoad;
    }

    protected function _beforeToHtml()
    {
        $this->loadData();
        return parent::_beforeToHtml();;
    }

    public function getMlmId()
    {
        $result = $this->cacheData[QLoad::A_MLM_ID] ?? '';
        return $result;
    }

    public function getParentCustId()
    {
        $result = $this->cacheData[QLoad::A_PARENT_CUST_ID] ?? '';
        return $result;
    }

    public function getParentEmail()
    {
        $result = $this->cacheData[QLoad::A_PARENT_EMAIL] ?? '';
        return $result;
    }

    public function getParentMlmId()
    {
        $result = $this->cacheData[QLoad::A_PARENT_MLM_ID] ?? '';
        return $result;
    }

    public function getParentName()
    {
        $first = $this->cacheData[QLoad::A_PARENT_FIRST] ?? '';
        $last = $this->cacheData[QLoad::A_PARENT_LAST] ?? '';
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
        $result = !empty($this->cacheData[QLoad::A_PARENT_MLM_ID]);
        return $result;
    }

    /**
     * Load block's working data before rendering.
     */
    private function loadData()
    {
        $custId = $this->registry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
        if ($custId) {
            $query = $this->qLoad->build();
            $conn = $query->getConnection();
            $bind = [
                QLoad::BND_CUST_ID => $custId
            ];
            $rs = $conn->fetchAll($query, $bind);
            $this->cacheData = reset($rs);
        }
    }
}