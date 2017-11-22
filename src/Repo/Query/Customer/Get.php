<?php

/**
 *
 */

namespace Praxigento\Downline\Repo\Query\Customer;


use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Entity\Data\Customer as EDwnlCust;

class Get
    extends \Praxigento\Core\Repo\Query\Builder
{
    const AS_DWNL_CUST = 'dwnlCust';
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_MAGE_CUST = 'mageCust';
    const A_EMAIL = 'email';
    const A_ID = 'id';
    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_MLM_ID = 'mlmId';
    const A_NAME_FIRST = 'nameFirst';
    const A_NAME_LAST = 'nameLast';
    /** Bound variables names ('camelCase' naming) */
    const BND_CUST_ID = 'custId';
    const E_DWNL_CUST = EDwnlCust::ENTITY_NAME;
    /** Entities are used in the query */
    const E_MAGE_CUST = Cfg::ENTITY_MAGE_CUSTOMER;

    /**
     * SELECT ...
     *
     * @inheritdoc
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCust = self::AS_MAGE_CUST;
        $asDwnl = self::AS_DWNL_CUST;

        /* FROM prxgt_dwnl_customer */
        $tbl = $this->resource->getTableName(EDwnlCust::ENTITY_NAME);
        $as = $asDwnl;
        $cols = [
            self::A_MLM_ID => EDwnlCust::ATTR_MLM_ID
        ];
        $result->from([$as => $tbl], $cols);


        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCust;
        $cols = [
            self::A_ID => Cfg::E_CUSTOMER_A_ENTITY_ID,
            self::A_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_NAME_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_NAME_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asDwnl . '.' . EDwnlCust::ATTR_CUSTOMER_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $result->where($asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=:' . self::BND_CUST_ID);

        return $result;

    }
}