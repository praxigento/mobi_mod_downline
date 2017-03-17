<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Query\Snap\OnDate;

use Praxigento\Downline\Data\Entity\Snap as Snap;
use Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder as MaxBuilder;

/**
 * Build query to get downline tree snap on given date.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Def\Builder
{
    /** Tables aliases. */
    const AS_DWNL_SNAP = 'prxgtDwnlSnap';
    const AS_DWNL_SNAP_4_MAX = \Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder::AS_DWNL_SNAP_4_MAX;
    const AS_DWNL_SNAP_MAX = 'prxgtDwnlSnapMax';

    /** Columns aliases. */
    const A_CUST_ID = Snap::ATTR_CUSTOMER_ID;
    const A_DEPTH = Snap::ATTR_DEPTH;
    const A_PARENT_ID = Snap::ATTR_PARENT_ID;
    const A_PATH = Snap::ATTR_PATH;

    /** Bound variables names */
    const BIND_ON_DATE = \Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder::BIND_ON_DATE;

    /** @var  \Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder */
    protected $qbldMax;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder $qbldMax
    ) {
        parent::__construct($resource);
        $this->qbldMax = $qbldMax;
    }

    /**
     * SELECT
     * `prxgtDwnlSnap`.`customer_id`,
     * `prxgtDwnlSnap`.`parent_id`,
     * `prxgtDwnlSnap`.`depth`,
     * `prxgtDwnlSnap`.`path`
     * FROM `prxgt_dwnl_snap` AS `prxgtDwnlSnap`
     * LEFT JOIN (SELECT
     * `prxgtDwnlSnap4Max`.`customer_id`,
     * (MAX(`prxgtDwnlSnap4Max`.`date`)) AS `date_max`
     * FROM `prxgt_dwnl_snap` AS `prxgtDwnlSnap4Max`
     * WHERE (prxgtDwnlSnap4Max.date <= :onDate)
     * GROUP BY `prxgtDwnlSnap4Max`.`customer_id`) AS `prxgtDwnlSnapMax`
     * ON (prxgtDwnlSnapMax.customer_id = prxgtDwnlSnap.customer_id)
     * AND (prxgtDwnlSnapMax.date_max = prxgtDwnlSnap.date)
     * WHERE (prxgtDwnlSnapMax.date_max IS NOT NULL)
     *
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getSelectQuery(\Praxigento\Core\Repo\Query\IBuilder $qbuild = null)
    {
        $result = $this->conn->select();
        /* define tables aliases */
        $asSnap = self::AS_DWNL_SNAP;
        $asMax = self::AS_DWNL_SNAP_MAX;

        /* select from prxgt_dwnl_snap */
        $tbl = $this->resource->getTableName(Snap::ENTITY_NAME);
        $cols = [
            self::A_CUST_ID => Snap::ATTR_CUSTOMER_ID,
            self::A_PARENT_ID => Snap::ATTR_PARENT_ID,
            self::A_DEPTH => Snap::ATTR_DEPTH,
            self::A_PATH => Snap::ATTR_PATH
        ];
        $result->from([$asSnap => $tbl], $cols);
        /* left join $q4Max */
        $q4Max = $this->qbldMax->getSelectQuery();
        $on = '(' . $asMax . '.' . MaxBuilder::A_CUST_ID . '=' . $asSnap . '.' . Snap::ATTR_CUSTOMER_ID . ')';
        $on .= ' AND (' . $asMax . '.' . MaxBuilder::A_DATE_MAX . '=' . $asSnap . '.' . Snap::ATTR_DATE . ')';
        $result->joinLeft([$asMax => $q4Max], $on, []);
        /* where */
        $result->where($asMax . '.' . MaxBuilder::A_DATE_MAX . ' IS NOT NULL');
        return $result;
    }
}