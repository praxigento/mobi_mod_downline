<?php

/**
 *
 */

namespace Praxigento\Downline\Repo\Query\Customer;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

class Get
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_DWNL_CUST = 'dwnlCust';
    const AS_MAGE_CUST = 'mageCust';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_COUNTRY = 'country';
    const A_EMAIL = 'email';
    const A_ID = 'id';
    const A_MLM_ID = 'mlmId';
    const A_NAME_FIRST = 'nameFirst';
    const A_NAME_LAST = 'nameLast';
    const A_PATH = 'path';

    /** Bound variables names ('camelCase' naming) */
    const BND_CUST_ID = 'custId';

    /** Entities are used in the query */
    const E_DWNL_CUST = EDwnlCust::ENTITY_NAME;
    const E_MAGE_CUST = Cfg::ENTITY_MAGE_CUSTOMER;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCust = self::AS_MAGE_CUST;
        $asDwnl = self::AS_DWNL_CUST;

        /* FROM prxgt_dwnl_customer */
        $tbl = $this->resource->getTableName(self::E_DWNL_CUST);
        $as = $asDwnl;
        $cols = [
            self::A_MLM_ID => EDwnlCust::A_MLM_ID,
            self::A_COUNTRY => EDwnlCust::A_COUNTRY_CODE,
            self::A_PATH => EDwnlCust::A_PATH
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(self::E_MAGE_CUST);
        $as = $asCust;
        $cols = [
            self::A_ID => Cfg::E_CUSTOMER_A_ENTITY_ID,
            self::A_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_NAME_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_NAME_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asDwnl . '.' . EDwnlCust::A_CUSTOMER_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $result->where($asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=:' . self::BND_CUST_ID);

        return $result;

    }
}