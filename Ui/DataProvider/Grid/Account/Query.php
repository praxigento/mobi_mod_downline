<?php
/**
 * Created by PhpStorm.
 * User: dm
 * Date: 07.09.17
 * Time: 9:57
 */

namespace Praxigento\Downline\Ui\DataProvider\Grid\Account;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer as EDownline;

class Query
    extends \Praxigento\Accounting\Ui\DataProvider\Grid\Account\Query
{

    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_DWNL_CUST = 'dc';
    const AS_DWNL_PARENT = 'dp';
    /**#@- */
    const A_CUST_COUNTRY = 'custCountry';
    /**#@+ Columns/expressions aliases for external usage */
    const A_CUST_MLM_ID = 'custMlmId';
    const A_PARENT_ID = 'parentId';
    const A_PARENT_MLM_ID = 'parentMlmId';

    /**#@- */


    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            /* init parent mapper */
            $this->mapper = parent::getMapper();
            /* then add own aliases */
            // custMlmId
            $key = self::A_CUST_MLM_ID;
            $value = self::AS_DWNL_CUST . '.' . EDownline::A_MLM_ID;
            $this->mapper->add($key, $value);
            // custCountry
            $key = self::A_CUST_COUNTRY;
            $value = self::AS_DWNL_CUST . '.' . EDownline::A_COUNTRY_CODE;
            $this->mapper->add($key, $value);
            // parentId
            $key = self::A_PARENT_ID;
            $value = self::AS_DWNL_PARENT . '.' . EDownline::A_CUSTOMER_REF;
            $this->mapper->add($key, $value);
            // parentMlmId
            $key = self::A_PARENT_MLM_ID;
            $value = self::AS_DWNL_PARENT . '.' . EDownline::A_MLM_ID;
            $this->mapper->add($key, $value);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        /* this is primary query builder, not extender */
        $result = parent::getQueryItems();

        /* define tables aliases for internal usage (in this method) */
        $asCust = self::AS_CUSTOMER;
        $asDwnlCust = self::AS_DWNL_CUST;
        $asDwnlParent = self::AS_DWNL_PARENT;

        /* LEFT JOIN prxgt_dwnl_customer AS cust */
        $tbl = $this->resource->getTableName(EDownline::ENTITY_NAME);
        $as = $asDwnlCust;
        $cols = [
            self::A_CUST_MLM_ID => EDownline::A_MLM_ID,
            self::A_CUST_COUNTRY => EDownline::A_COUNTRY_CODE
        ];
        $cond = $as . '.' . EDownline::A_CUSTOMER_REF . '=' . $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer AS parent */
        $tbl = $this->resource->getTableName(EDownline::ENTITY_NAME);
        $as = $asDwnlParent;
        $cols = [
            self::A_PARENT_ID => EDownline::A_CUSTOMER_REF,
            self::A_PARENT_MLM_ID => EDownline::A_MLM_ID
        ];
        $cond = $as . '.' . EDownline::A_CUSTOMER_REF . '=' . $asDwnlCust . '.' . EDownline::A_PARENT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* return  result */
        return $result;
    }
}