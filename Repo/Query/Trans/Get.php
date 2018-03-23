<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Query\Trans;

use Praxigento\Accounting\Repo\Data\Account as Acc;
use Praxigento\Downline\Repo\Data\Customer as Cust;

/**
 * Build query to get transactions for the customer (extends accounting query).
 */
class Get
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /**
     * Tables aliases.
     */
    const AS_CUST_CRD = 'custCrd';
    const AS_CUST_DBT = 'custDbt';
    /**
     * Attributes aliases.
     */
    const A_CREDIT_CUST_REF = 'creditCustRef';
    const A_DEBIT_CUST_REF = 'debitCustRef';
    /** @var \Praxigento\Accounting\Repo\Query\Trans\Get\Builder */
    protected $qbuildAccTrans;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Repo\Query\Trans\Get\Builder $qbldAccTrans
    )
    {
        parent::__construct($resource);
        $this->qbuildAccTrans = $qbldAccTrans;
    }

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = is_null($source)
            ? $this->qbuildAccTrans->build()
            : $source;
        /* create shortcuts for table aliases */
        $asCustCrd = self::AS_CUST_CRD;
        $asCustDbt = self::AS_CUST_DBT;
        $asAccCrd = \Praxigento\Accounting\Repo\Query\Trans\Get\Builder::AS_ACC_CRD;
        $asAccDbt = \Praxigento\Accounting\Repo\Query\Trans\Get\Builder::AS_ACC_DBT;

        /* LEFT JOIN prxgt_dwnl_customer custDb */
        $tbl = $this->resource->getTableName(Cust::ENTITY_NAME);
        $on = $asCustDbt . '.' . Cust::A_CUSTOMER_ID . '=' . $asAccDbt . '.' . Acc::A_CUST_ID;
        $cols = [self::A_DEBIT_CUST_REF => Cust::A_MLM_ID];
        $result->joinLeft([$asCustDbt => $tbl], $on, $cols);
        /* LEFT JOIN prxgt_dwnl_customer custCr */
        $tbl = $this->resource->getTableName(Cust::ENTITY_NAME);
        $on = $asCustCrd . '.' . Cust::A_CUSTOMER_ID . '=' . $asAccCrd . '.' . Acc::A_CUST_ID;
        $cols = [self::A_CREDIT_CUST_REF => Cust::A_MLM_ID];
        $result->joinLeft([$asCustCrd => $tbl], $on, $cols);

        /* result */
        return $result;
    }

}