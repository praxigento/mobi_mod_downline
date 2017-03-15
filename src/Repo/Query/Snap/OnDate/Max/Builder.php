<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Query\Snap\OnDate\Max;

use Praxigento\Downline\Data\Entity\Snap as Snap;

/**
 * Build query to get the last changed dates by customer ID for downline tree.
 *
 * This query is used as sub-query to get on"-date" snapshots.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Def\Builder
{
    const AS_DWNL_SNAP_4_MAX = 'prxgtDwnlSnap4Max';
    const A_CUST_ID = Snap::ATTR_CUSTOMER_ID;
    const A_DATE_MAX = 'date_max';
    const BIND_ON_DATE = 'onDate';

    /**
     * SELECT
     * `prxgtDwnlSnap4Max`.`customer_id`,
     * (MAX(`prxgtDwnlSnap4Max`.`date`)) AS `date_max`
     * FROM `prxgt_dwnl_snap` AS `prxgtDwnlSnap4Max`
     * WHERE (prxgtDwnlSnap4Max.date <= :onDate)
     * GROUP BY `prxgtDwnlSnap4Max`.`customer_id`
     *
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getSelectQuery(\Praxigento\Core\Repo\Query\IBuilder $qbuild = null)
    {
        $result = $this->conn->select();
        /* define tables aliases */
        $asSnap = self::AS_DWNL_SNAP_4_MAX;

        /* select MAX(date) from prxgt_dwnl_snap (internal select) */
        $tbl = $this->resource->getTableName(Snap::ENTITY_NAME);
        $expMaxDate = new \Praxigento\Core\Repo\Query\Expression(
            'MAX(`' . $asSnap . '`.`' . Snap::ATTR_DATE . '`)'
        );
        $cols = [
            self::A_CUST_ID => Snap::ATTR_CUSTOMER_ID,
            self::A_DATE_MAX => $expMaxDate
        ];
        $result->from([$asSnap => $tbl], $cols);

        /* query tuning */
        $result->group($asSnap . '.' . Snap::ATTR_CUSTOMER_ID);
        $result->where($asSnap . '.' . Snap::ATTR_DATE . '<=:' . self::BIND_ON_DATE);

        return $result;
    }
}