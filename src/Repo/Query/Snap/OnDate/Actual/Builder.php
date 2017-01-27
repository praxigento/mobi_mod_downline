<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Query\Snap\OnDate\Actual;

use Praxigento\Downline\Data\Entity\Customer as Customer;
use Praxigento\Downline\Data\Entity\Snap as Snap;

/**
 * Build query to get actual snap for downline tree.
 *
 * see \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Def\Builder
{
    const AS_TBL_DWNL_SNAP = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_TBL_DWNL_SNAP;

    /**
     * SELECT
     * `prxgtDwnlSnap`.`customer_id`,
     * `prxgtDwnlSnap`.`parent_id`,
     * `prxgtDwnlSnap`.`depth`,
     * `prxgtDwnlSnap`.`path`
     * FROM `prxgt_dwnl_customer` AS `prxgtDwnlSnap`
     *
     * @inheritdoc
     */
    public function getSelectQuery(\Praxigento\Core\Repo\Query\IBuilder $qbuild = null)
    {
        $asSnap = self::AS_TBL_DWNL_SNAP;
        $tblSnap = $this->resource->getTableName(Customer::ENTITY_NAME);
        /* select from prxgt_dwnl_snap */
        $result = $this->conn->select();
        /* base queries for snap data should have the same attributes names (see ../Builder) */
        $result->from([$asSnap => $tblSnap], [
            Snap::ATTR_CUSTOMER_ID => Customer::ATTR_CUSTOMER_ID,
            Snap::ATTR_PARENT_ID => Customer::ATTR_PARENT_ID,
            Snap::ATTR_DEPTH => Customer::ATTR_DEPTH,
            Snap::ATTR_PATH => Customer::ATTR_PATH
        ]);
        return $result;
    }
}