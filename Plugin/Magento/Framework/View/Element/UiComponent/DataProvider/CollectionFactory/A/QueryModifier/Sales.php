<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory\A\QueryModifier;

use Praxigento\Core\App\Repo\Query\Expression as AnExpression;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

class Sales
{
    /** Tables aliases for external usage ('camelCase' naming) */
    private const AS_CUST = 'prxgtDwnlCust';
    private const AS_PARENT = 'prxgtDwnlParent';
    private const AS_MAGE_PARENT = 'mageCustParent';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    private const A_COUNTRY = 'prxgtDwnlCountry';
    private const A_MLM_ID = 'prxgtDwnlMlmId';
    private const A_PARENT_MLM_ID = 'prxgtDwnlMlmIdParent';
    private const A_PARENT_NAME = 'prxgtDwnlParentName';

    /** Entities are used in the query */
    private const E_CUST = EDwnlCust::ENTITY_NAME;
    private const E_MAGE_PARENT = Cfg::ENTITY_MAGE_CUSTOMER;
    private const E_PARENT = EDwnlCust::ENTITY_NAME;

    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function addFieldsMapping(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        /* prxgtDwnlCountry */
        $fieldAlias = self::A_COUNTRY;
        $fieldFullName = self::AS_CUST . '.' . EDwnlCust::A_COUNTRY_CODE;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* prxgtDwnlMlmId */
        $fieldAlias = self::A_MLM_ID;
        $fieldFullName = self::AS_CUST . '.' . EDwnlCust::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* prxgtDwnlMlmIdParent */
        $fieldAlias = self::A_PARENT_MLM_ID;
        $fieldFullName = self::AS_PARENT . '.' . EDwnlCust::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* prxgtDwnlParentName */
        $fieldAlias = self::A_PARENT_NAME;
        $fieldFullName = $this->expCustName();
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
    }

    public function populateSelect(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        $result = $collection->getSelect();
        $asCust = self::AS_CUST;
        $asParent = self::AS_PARENT;
        $asParentName = self::AS_MAGE_PARENT;

        /* LEFT JOIN prxgt_dwnl_customer (for customer MLM ID) */
        $tbl = $this->resource->getTableName(self::E_CUST);
        $as = $asCust;
        $cols = [
            self::A_COUNTRY => EDwnlCust::A_COUNTRY_CODE,
            self::A_MLM_ID => EDwnlCust::A_MLM_ID,
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_REF . "=" . Cfg::AS_MAIN_TABLE . "." . Cfg::E_SALE_ORDER_A_CUSTOMER_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer (for parent MLM ID) */
        $tbl = $this->resource->getTableName(self::E_PARENT);
        $as = $asParent;
        $cols = [
            self::A_PARENT_MLM_ID => EDwnlCust::A_MLM_ID
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_REF . "=$asCust." . EDwnlCust::A_PARENT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity (for parent name) */
        $tbl = $this->resource->getTableName(self::E_MAGE_PARENT);
        $as = $asParentName;
        $cols = [
            self::A_PARENT_NAME => $this->expCustName()
        ];
        $cond = "$as." . Cfg::E_CUSTOMER_A_ENTITY_ID . "=$asCust." . EDwnlCust::A_PARENT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

    private function expCustName() {
        $name = 'CONCAT(' . self::AS_MAGE_PARENT . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME . ', " ", '
            . self::AS_MAGE_PARENT . '.' . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $result = new AnExpression($name);
        return $result;
    }

}
