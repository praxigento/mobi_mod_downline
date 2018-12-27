<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Repo\Query\Account\Trans;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

/**
 * This pair use generalization instead of wrapping (see. \Praxigento\Accounting\Repo\Query\Trans\Get\ForCustomer).
 */
class Get
    extends \Praxigento\Accounting\Repo\Query\Trans\Get
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_DWNL_CREDIT = 'dwnlCred';
    const AS_DWNL_DEBIT = 'dwnlDebt';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_CREDIT_MLM_ID = 'creditMlmId';
    const A_DEBIT_MLM_ID = 'debitMlmId';

    /** Entities are used in the query */
    const E_DWNL_CREDIT = EDwnlCust::ENTITY_NAME;
    const E_DWNL_DEBIT = EDwnlCust::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = parent::build($source);

        /* define tables aliases for internal usage (in this method) */
        $asAccCredit = self::AS_ACC_CREDIT;
        $asAccDebit = self::AS_ACC_DEBIT;
        $asDwnlCredit = self::AS_DWNL_CREDIT;
        $asDwnlDebit = self::AS_DWNL_DEBIT;

        /* LEFT JOIN prxgt_dwnl_customer (debit) */
        $tbl = $this->resource->getTableName(self::E_DWNL_DEBIT);
        $as = $asDwnlDebit;
        $cols = [
            self::A_DEBIT_MLM_ID => EDwnlCust::A_MLM_ID
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_REF . "=$asAccDebit." . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer (debit) */
        $tbl = $this->resource->getTableName(self::E_DWNL_CREDIT);
        $as = $asDwnlCredit;
        $cols = [
            self::A_CREDIT_MLM_ID => EDwnlCust::A_MLM_ID
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_REF . "=$asAccCredit." . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

}