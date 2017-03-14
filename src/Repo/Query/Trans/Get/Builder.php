<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Query\Trans\Get;

use Praxigento\Accounting\Data\Entity\Account as Acc;
use Praxigento\Downline\Data\Entity\Customer as Cust;

/**
 * Build query to get transactions for the customer.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Def\Builder
{
    /**
     * Tables aliases.
     */
    const AS_CUST_CRD = 'custCrd';
    const AS_CUST_DBT = 'custDbt';

    /** @var \Praxigento\Accounting\Repo\Query\Trans\Get\Builder */
    protected $qbuildAccTrans;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Repo\Query\Trans\Get\Builder $qbuildAccTrans
    ) {
        parent::__construct($resource);
        $this->qbuildAccTrans = $qbuildAccTrans;
    }

    /**
     * Attributes aliases.
     */
    const A_CREDIT_CUST_REF = 'creditCustRef';
    const A_DEBIT_CUST_REF = 'debitCustRef';

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getSelectQuery(\Praxigento\Core\Repo\Query\IBuilder $qbuild = null)
    {
        $result = is_null($qbuild)
            ? $this->qbuildAccTrans->getSelectQuery()
            : $qbuild->getSelectQuery();
        /* create shortcuts for table aliases */
        $asCustCrd = self::AS_CUST_CRD;
        $asCustDbt = self::AS_CUST_DBT;
        $asAccCrd = \Praxigento\Accounting\Repo\Query\Trans\Get\Builder::AS_ACC_CRD;
        $asAccDbt = \Praxigento\Accounting\Repo\Query\Trans\Get\Builder::AS_ACC_DBT;

        /* LEFT JOIN prxgt_dwnl_customer custDb */
        $tbl = $this->resource->getTableName(Cust::ENTITY_NAME);
        $on = $asCustDbt . '.' . Cust::ATTR_CUSTOMER_ID . '=' . $asAccDbt . '.' . Acc::ATTR_CUST_ID;
        $cols = [self::A_DEBIT_CUST_REF => Cust::ATTR_HUMAN_REF];
        $result->joinLeft([$asCustDbt => $tbl], $on, $cols);
        /* LEFT JOIN prxgt_dwnl_customer custCr */
        $tbl = $this->resource->getTableName(Cust::ENTITY_NAME);
        $on = $asCustCrd . '.' . Cust::ATTR_CUSTOMER_ID . '=' . $asAccCrd . '.' . Acc::ATTR_CUST_ID;
        $cols = [self::A_CREDIT_CUST_REF => Cust::ATTR_HUMAN_REF];
        $result->joinLeft([$asCustCrd => $tbl], $on, $cols);

        /* result */
        return $result;
    }
}