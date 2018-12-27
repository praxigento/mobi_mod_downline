<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory\A\QueryModifier;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

class Sales
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CUST = 'prxgtDwnlCust';
    const AS_PARENT = 'prxgtDwnlParent';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_MLM_ID = 'prxgtDwnlMlmId';
    const A_PARENT_MLM_ID = 'prxgtDwnlMlmIdParent';

    /** Entities are used in the query */
    const E_CUST = EDwnlCust::ENTITY_NAME;
    const E_PARENT = EDwnlCust::ENTITY_NAME;

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
        /* prxgtDwnlMlmId */
        $fieldAlias = self::A_MLM_ID;
        $fieldFullName = self::AS_CUST . '.' . EDwnlCust::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* prxgtDwnlMlmIdParent */
        $fieldAlias = self::A_PARENT_MLM_ID;
        $fieldFullName = self::AS_PARENT . '.' . EDwnlCust::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
    }

    public function populateSelect(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        $result = $collection->getSelect();
        $asCust = self::AS_CUST;
        $asParent = self::AS_PARENT;

        /* LEFT JOIN prxgt_dwnl_customer (for customer MLM ID) */
        $tbl = $this->resource->getTableName(self::E_CUST);
        $as = $asCust;
        $cols = [
            self::A_MLM_ID => EDwnlCust::A_MLM_ID
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

        return $result;
    }

}