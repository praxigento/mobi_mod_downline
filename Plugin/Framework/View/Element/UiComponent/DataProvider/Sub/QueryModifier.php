<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer;

class QueryModifier
{
    const AS_FLD_CUSTOMER_DEPTH = 'prxgtDwnlCustomerDepth';
    const AS_FLD_CUSTOMER_REF = 'prxgtDwnlCustomerRef';
    const AS_FLD_PARENT_ID = 'prxgtDwnlParentId';
    const AS_FLD_PARENT_REF = 'prxgtDwnlParentRef';
    const AS_TBL_CUST = 'prxgtDwnlCust';
    const AS_TBL_PARENT_CUST = 'prxgtDwnlParentCust';

    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function addFieldsMapping(
        \Magento\Customer\Model\ResourceModel\Grid\Collection $collection
    ) {
        // depth
        $fieldAlias = self::AS_FLD_CUSTOMER_DEPTH;
        $fieldFullName = self::AS_TBL_CUST . '.' . Customer::A_DEPTH;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        // ref (mlm id)
        $fieldAlias = self::AS_FLD_CUSTOMER_REF;
        $fieldFullName = self::AS_TBL_CUST . '.' . Customer::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        // parent id
        $fieldAlias = self::AS_FLD_PARENT_ID;
        $fieldFullName = self::AS_TBL_CUST . '.' . Customer::A_PARENT_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        // parent ref (mlm id)
        $fieldAlias = self::AS_FLD_PARENT_REF;
        $fieldFullName = self::AS_TBL_PARENT_CUST . '.' . Customer::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
    }

    public function populateSelect(
        \Magento\Customer\Model\ResourceModel\Grid\Collection $collection
    ) {
        $select = $collection->getSelect();
        /* LEFT JOIN `prxgt_dwnl_customer` AS `prxgtDwnlCust` */
        $tbl = [self::AS_TBL_CUST => $this->_resource->getTableName(Customer::ENTITY_NAME)];
        $on = self::AS_TBL_CUST . '.' . Customer::A_CUSTOMER_ID . '=main_table.' . Cfg::E_CUSTOMER_A_ENTITY_ID;
        $cols = [
            self::AS_FLD_CUSTOMER_REF => Customer::A_MLM_ID,
            self::AS_FLD_CUSTOMER_DEPTH => Customer::A_DEPTH,
            self::AS_FLD_PARENT_ID => Customer::A_PARENT_ID
        ];
        $select->joinLeft($tbl, $on, $cols);
        /* LEFT JOIN `prxgt_dwnl_customer` AS `prxgtDwnlParentCust` */
        $tbl = [self::AS_TBL_PARENT_CUST => $this->_resource->getTableName(Customer::ENTITY_NAME)];
        $on = self::AS_TBL_PARENT_CUST . '.' . Customer::A_CUSTOMER_ID . '=' . self::AS_TBL_CUST . '.' . Customer::A_PARENT_ID;
        $cols = [
            self::AS_FLD_PARENT_REF => Customer::A_MLM_ID
        ];
        $select->joinLeft($tbl, $on, $cols);
        // $sql = (string)$query;
        return $select;
    }

}