<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Query\Snap\OnDate;

use Praxigento\Downline\Repo\Entity\Data\Snap as ESnap;
use Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder as MaxBuilder;

/**
 * Build query to get downline tree snap on given date.
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases. */
    const AS_DWNL_SNAP = 'prxgtDwnlSnap';
    const AS_DWNL_SNAP_4_MAX = MaxBuilder::AS_DWNL_SNAP_4_MAX;
    const AS_DWNL_SNAP_MAX = 'prxgtDwnlSnapMax';

    /** Columns aliases. */
    /* this aliases must be equals to ESnap::ATTR_, */
    /* see \Praxigento\Downline\Service\Snap\Sub\CalcSimple::calcSnapshots */
    const A_CUST_ID = ESnap::ATTR_CUSTOMER_ID;
    const A_DEPTH = ESnap::ATTR_DEPTH;
    const A_PARENT_ID = ESnap::ATTR_PARENT_ID;
    const A_PATH = ESnap::ATTR_PATH;

    /** Bound variables names */
    const BND_ON_DATE = MaxBuilder::BND_ON_DATE;

    /** @var  \Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder */
    protected $qbldMax;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder $qbldMax
    )
    {
        parent::__construct($resource);
        $this->qbldMax = $qbldMax;
    }

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();
        /* define tables aliases */
        $asSnap = self::AS_DWNL_SNAP;
        $asMax = self::AS_DWNL_SNAP_MAX;

        /* select from prxgt_dwnl_snap */
        $tbl = $this->resource->getTableName(ESnap::ENTITY_NAME);
        $cols = [
            self::A_CUST_ID => ESnap::ATTR_CUSTOMER_ID,
            self::A_PARENT_ID => ESnap::ATTR_PARENT_ID,
            self::A_DEPTH => ESnap::ATTR_DEPTH,
            self::A_PATH => ESnap::ATTR_PATH
        ];
        $result->from([$asSnap => $tbl], $cols);
        /* left join $q4Max */
        $q4Max = $this->qbldMax->build();
        $on = '(' . $asMax . '.' . MaxBuilder::A_CUST_ID . '=' . $asSnap . '.' . ESnap::ATTR_CUSTOMER_ID . ')';
        $on .= ' AND (' . $asMax . '.' . MaxBuilder::A_DATE_MAX . '=' . $asSnap . '.' . ESnap::ATTR_DATE . ')';
        $result->joinLeft([$asMax => $q4Max], $on, []);
        /* where */
        $result->where($asMax . '.' . MaxBuilder::A_DATE_MAX . ' IS NOT NULL');
        return $result;
    }
}