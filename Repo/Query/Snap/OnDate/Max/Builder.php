<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Query\Snap\OnDate\Max;

use Praxigento\Downline\Repo\Data\Snap as ESnap;

/**
 * Build query to get the last changed dates by customer ID for downline tree.
 *
 * This query is used as sub-query to get on-date snapshots.
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_DWNL_SNAP_4_MAX = 'prxgtDwnlSnap4Max';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_CUST_ID = ESnap::A_CUSTOMER_ID;
    const A_DATE_MAX = 'date_max';

    /** Bound variables names ('camelCase' naming) */
    const BND_ON_DATE = 'onDate';


    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();
        /* define tables aliases */
        $asSnap = self::AS_DWNL_SNAP_4_MAX;

        /* select MAX(date) from prxgt_dwnl_snap (internal select) */
        $tbl = $this->resource->getTableName(ESnap::ENTITY_NAME);
        $expMaxDate = new \Praxigento\Core\App\Repo\Query\Expression(
            'MAX(`' . $asSnap . '`.`' . ESnap::A_DATE . '`)'
        );
        $cols = [
            self::A_CUST_ID => ESnap::A_CUSTOMER_ID,
            self::A_DATE_MAX => $expMaxDate
        ];
        $result->from([$asSnap => $tbl], $cols);

        /* query tuning */
        $result->group($asSnap . '.' . ESnap::A_CUSTOMER_ID);
        $result->where($asSnap . '.' . ESnap::A_DATE . '<=:' . self::BND_ON_DATE);

        return $result;
    }
}