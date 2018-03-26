<?php
/**
 * Created by PhpStorm.
 * User: dm
 * Date: 07.09.17
 * Time: 9:57
 */

namespace Praxigento\Downline\Ui\DataProvider\Grid\Account;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Downline\Repo\Data\Customer as EDownline;

class Query
    extends \Praxigento\Accounting\Ui\DataProvider\Grid\Account\Query
{

    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_DWNL = 'dwnl';
    /**#@- */

    /**#@+ Columns/expressions aliases for external usage */
    const A_MLMID = 'mlmId';

    /**#@- */


    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            /* init parent mapper */
            $this->mapper = parent::getMapper();
            /* then add own aliases */
            $key = self::A_MLMID;
            $value = self::AS_DWNL . '.' . EDownline::A_MLM_ID;
            $this->mapper->add($key, $value);
        }
        $result = $this->mapper;
        return $result;
    }

    /**
     * SELECT
     * ...
     * FROM
     * `prxgt_acc_account` AS `paa`
     * LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON
     * pata.id = paa.asset_type_id
     * LEFT JOIN `customer_entity` AS `ce` ON
     * ce.entity_id = paa.customer_id
     * LEFT JOIN `prxgt_dwnl_customer` AS `dwnl` ON
     * dwnl.customer_ref = paa.customer_id
     */
    protected function getQueryItems()
    {
        /* this is primary query builder, not extender */
        $result = parent::getQueryItems();

        /* define tables aliases for internal usage (in this method) */
        $asDwnl = self::AS_DWNL;
        $asAcc = self::AS_ACCOUNT;

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(EDownline::ENTITY_NAME);
        $as = $asDwnl;
        $cols = [
            self::A_MLMID => EDownline::A_MLM_ID
        ];
        $cond = $as . '.' . EDownline::A_CUSTOMER_ID . '=' . $asAcc . '.' . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* return  result */
        return $result;
    }
}