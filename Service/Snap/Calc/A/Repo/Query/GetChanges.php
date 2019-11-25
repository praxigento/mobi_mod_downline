<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Service\Snap\Calc\A\Repo\Query;

use Praxigento\Downline\Repo\Data\Change as EChange;

class GetChanges
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CHANGE = 'ch';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_CUSTOMER_ID = EChange::A_CUSTOMER_REF;
    const A_DATE_CHANGED = EChange::A_DATE_CHANGED;
    const A_ID = EChange::A_ID;
    const A_PARENT_ID = EChange::A_PARENT_REF;

    /** Bound variables names ('camelCase' naming) */
    const BND_DATE_FROM = 'dateFrom';
    const BND_DATE_TO = 'dateTo';

    /** Entities are used in the query */
    const E_CHANGE = EChange::ENTITY_NAME;

    /**
     * SELECT
     * `ch`.`customer_ref`,
     * `ch`.`date_changed`,
     * `ch`.`id`,
     * `ch`.`parent_ref`
     * FROM
     * `prxgt_dwnl_change` AS `ch`
     * WHERE
     * ((ch.date_changed >=:dateFrom)
     * AND (ch.date_changed<:dateTo))
     * ORDER BY
     * `ch`.`date_changed` ASC,
     * `ch`.`customer_ref` ASC,
     * `ch`.`id` ASC
     *
     * @param \Magento\Framework\DB\Select|null $source
     * @return \Magento\Framework\DB\Select
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asChange = self::AS_CHANGE;

        /* FROM prxgt_dwnl_change */
        $tbl = $this->resource->getTableName(self::E_CHANGE);    // name with prefix
        $as = $asChange;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_CUSTOMER_ID => EChange::A_CUSTOMER_REF,
            self::A_DATE_CHANGED => EChange::A_DATE_CHANGED,
            self::A_ID => EChange::A_ID,
            self::A_PARENT_ID => EChange::A_PARENT_REF
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* WHERE */
        $byFrom = "$asChange." . EChange::A_DATE_CHANGED . '>=:' . self::BND_DATE_FROM;
        $byTo = "$asChange." . EChange::A_DATE_CHANGED . '<:' . self::BND_DATE_TO;
        $result->where("($byFrom) AND ($byTo)");

        /**
         * ORDER:
         *
         * by date changed, then by change log ID (SAN-470), then by customer ID.
         * Order is important for tree snapshot calculation (MOBI-202)
         */
        $result->order([
            $asChange . '.' . EChange::A_DATE_CHANGED . ' ASC',
            $asChange . '.' . EChange::A_ID . ' ASC',
            $asChange . '.' . EChange::A_CUSTOMER_REF . ' ASC'
        ]);

        return $result;
    }


}