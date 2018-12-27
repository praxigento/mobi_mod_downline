<?php
/**
 * Created by PhpStorm.
 * User: dm
 * Date: 07.09.17
 * Time: 9:57
 */

namespace Praxigento\Downline\Ui\DataProvider\Grid\Transaction;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Downline\Repo\Data\Customer as EDownline;

class Query
    extends \Praxigento\Accounting\Ui\DataProvider\Grid\Transaction\Query
{

    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_DWNL_CRED = 'dwnlCredit';
    const AS_DWNL_DEB = 'dwnlDebit';

    /** Columns/expressions aliases for external usage */
    const A_MLM_ID_CREDIT = 'mlmIdCredit';
    const A_MLM_ID_DEBIT = 'mlmIdDebit';

    /** Entities are used in the query */
    const E_DWNL_CUST = EDownline::ENTITY_NAME;

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            /* init parent mapper */
            $this->mapper = parent::getMapper();
            /* then add own aliases */
            $key = self::A_MLM_ID_DEBIT;
            $value = self::AS_DWNL_DEB . '.' . EDownline::A_MLM_ID;
            $this->mapper->add($key, $value);
            $key = self::A_MLM_ID_CREDIT;
            $value = self::AS_DWNL_CRED . '.' . EDownline::A_MLM_ID;
            $this->mapper->add($key, $value);
        }
        $result = $this->mapper;
        return $result;
    }

    /**
     * SELECT
     * ...
     * FROM
     * `prxgt_acc_transaction` AS `pat`
     * LEFT JOIN `prxgt_acc_account` AS `paa_db` ON
     * paa_db.id = pat.debit_acc_id
     * LEFT JOIN `customer_entity` AS `ce_db` ON
     * ce_db.entity_id = paa_db.customer_id
     * LEFT JOIN `prxgt_acc_account` AS `paa_cr` ON
     * paa_cr.id = pat.credit_acc_id
     * LEFT JOIN `customer_entity` AS `ce_cr` ON
     * ce_cr.entity_id = paa_cr.customer_id
     * LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON
     * pata.id = paa_db.asset_type_id
     * LEFT JOIN `prxgt_dwnl_customer` AS `dwnlDebit` ON
     * dwnlDebit.customer_ref = paa_db.customer_id
     * LEFT JOIN `prxgt_dwnl_customer` AS `dwnlCredit` ON
     * dwnlCredit.customer_ref = paa_cr.customer_id
     */
    protected function getQueryItems()
    {
        /* this is primary query builder, not extender */
        $result = parent::getQueryItems();

        /* define tables aliases for internal usage (in this method) */
        $asAccCred = self::AS_ACC_CREDIT;
        $asAccDeb = self::AS_ACC_DEBIT;
        $asDwnlCred = self::AS_DWNL_CRED;
        $asDwnlDeb = self::AS_DWNL_DEB;

        /* LEFT JOIN prxgt_dwnl_customer as debit */
        $tbl = $this->resource->getTableName(self::E_DWNL_CUST);
        $as = $asDwnlDeb;
        $cols = [
            self::A_MLM_ID_DEBIT => EDownline::A_MLM_ID
        ];
        $cond = "$as." . EDownline::A_CUSTOMER_REF . '=' . $asAccDeb . '.' . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer as credit */
        $tbl = $this->resource->getTableName(self::E_DWNL_CUST);
        $as = $asDwnlCred;
        $cols = [
            self::A_MLM_ID_CREDIT => EDownline::A_MLM_ID
        ];
        $cond = "$as." . EDownline::A_CUSTOMER_REF . '=' . $asAccCred . '.' . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* return  result */
        return $result;
    }
}