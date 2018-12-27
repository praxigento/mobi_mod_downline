<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info\A\Repo\Query;

use Praxigento\Downline\Repo\Data\Customer as EDwnl;
use Praxigento\Santegra\Config as Cfg;

class Load
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CUST = 'cust';
    const AS_CUST_PARENT = 'custParent';
    const AS_DWNL_CUST = 'dwnlCust';
    const AS_DWNL_PARENT = 'dwnlParent';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_MLM_ID = 'mlmId';
    const A_PARENT_CUST_ID = 'parentCustId';
    const A_PARENT_EMAIL = 'parentEmail';
    const A_PARENT_FIRST = 'parentFirst';
    const A_PARENT_LAST = 'parentLast';
    const A_PARENT_MLM_ID = 'parenMlmId';

    /** Bound variables names ('camelCase' naming) */
    const BND_CUST_ID = 'custId';

    /** Entities are used in the query */
    const E_CUST = Cfg::ENTITY_MAGE_CUSTOMER;
    const E_DWNL = EDwnl::ENTITY_NAME;

    /**
     * SELECT `dwnlCust`.`mlm_id`      AS `mlmId`,
     * `dwnlParent`.`mlm_id`    AS `parenMlmId`,
     * `custParent`.`entity_id` AS `parentCustId`,
     * `custParent`.`email`     AS `parentEmail`,
     * `custParent`.`firstname` AS `parentFirst`,
     * `custParent`.`lastname`  AS `parentLast`
     * FROM `customer_entity` AS `cust`
     * LEFT JOIN `prxgt_dwnl_customer` AS `dwnlCust` ON dwnlCust.customer_ref = cust.entity_id
     * LEFT JOIN `prxgt_dwnl_customer` AS `dwnlParent` ON dwnlParent.customer_ref = dwnlCust.parent_ref
     * LEFT JOIN `customer_entity` AS `custParent` ON custParent.entity_id = dwnlCust.parent_ref
     * WHERE (cust.entity_id = :custId)
     *
     * @inheritdoc
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCust = self::AS_CUST;
        $asCustParent = self::AS_CUST_PARENT;
        $asDwnlCust = self::AS_DWNL_CUST;
        $asDwnlParent = self::AS_DWNL_PARENT;

        /* FROM customer_entity */
        $tbl = $this->resource->getTableName(self::E_CUST);    // name with prefix
        $as = $asCust;    // alias for 'current table' (currently processed in this block of code)
        $cols = [];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN `prxgt_dwnl_customer` AS `dwnlCust` */
        $tbl = $this->resource->getTableName(self::E_DWNL);
        $as = $asDwnlCust;
        $cols = [
            self::A_MLM_ID => EDwnl::A_MLM_ID
        ];
        $cond = "$as." . EDwnl::A_CUSTOMER_REF . "=$asCust." . Cfg::E_CUSTOMER_A_ENTITY_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN `prxgt_dwnl_customer` AS `dwnlParent` */
        $tbl = $this->resource->getTableName(self::E_DWNL);
        $as = $asDwnlParent;
        $cols = [
            self::A_PARENT_MLM_ID => EDwnl::A_MLM_ID
        ];
        $cond = "$as." . EDwnl::A_CUSTOMER_REF . "=$asDwnlCust." . EDwnl::A_PARENT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN `customer_entity` AS `custParent` */
        $tbl = $this->resource->getTableName(self::E_CUST);
        $as = $asCustParent;
        $cols = [
            self::A_PARENT_CUST_ID => Cfg::E_CUSTOMER_A_ENTITY_ID,
            self::A_PARENT_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_PARENT_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_PARENT_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $cond = "$as." . Cfg::E_CUSTOMER_A_ENTITY_ID . "=$asDwnlCust." . EDwnl::A_PARENT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byCust = "$asCust." . Cfg::E_CUSTOMER_A_ENTITY_ID . "=:" . self::BND_CUST_ID;
        $result->where($byCust);

        return $result;
    }

}