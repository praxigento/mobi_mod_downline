<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Ui\DataProvider\Grid\Customer\Group\Changes;

use Praxigento\Core\App\Repo\Query\Expression as AnExpression;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Change\Group as EChangeGroup;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

class Query
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CUST = 'c';
    const AS_DWNL = 'd';
    const AS_GRP_CUR = 'gc';
    const AS_GRP_NEW = 'gn';
    const AS_GRP_OLD = 'go';
    const AS_REG = 'r';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_CUST_ID = 'custId';
    const A_CUST_MLM_ID = 'custMlmId';
    const A_CUST_NAME = 'custName';
    const A_DATE_CHANGED = 'dateChanged';
    const A_GROUP_CUR = 'groupCur';
    const A_GROUP_NEW = 'groupNew';
    const A_GROUP_OLD = 'groupOld';
    const A_ID = 'id';

    /** Entities are used in the query */
    const E_CUST = Cfg::ENTITY_MAGE_CUSTOMER;
    const E_CUST_GROUP = Cfg::ENTITY_MAGE_CUSTOMER_GROUP;
    const E_DWNL = EDwnlCust::ENTITY_NAME;
    const E_REG = EChangeGroup::ENTITY_NAME;

    private function expCustName()
    {
        $name = 'CONCAT(' . self::AS_CUST . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME . ', " ", '
            . self::AS_CUST . '.' . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $result = new AnExpression($name);
        return $result;
    }

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $expCustName = $this->expCustName();
            $map = [
                self::A_CUST_ID => self::AS_REG . '.' . EChangeGroup::A_CUSTOMER_REF,
                self::A_CUST_MLM_ID => self::AS_DWNL . '.' . EDwnlCust::A_MLM_ID,
                self::A_CUST_NAME => $expCustName,
                self::A_DATE_CHANGED => self::AS_REG . '.' . EChangeGroup::A_DATE_CHANGED,
                self::A_GROUP_CUR => self::AS_GRP_CUR . '.' . Cfg::E_CUSTGROUP_A_CODE,
                self::A_GROUP_NEW => self::AS_GRP_NEW . '.' . Cfg::E_CUSTGROUP_A_CODE,
                self::A_GROUP_OLD => self::AS_GRP_OLD . '.' . Cfg::E_CUSTGROUP_A_CODE,
                self::A_ID => self::AS_REG . '.' . EChangeGroup::A_ID
            ];
            $this->mapper = new \Praxigento\Core\App\Repo\Query\Criteria\Def\Mapper($map);
        }
        $result = $this->mapper;
        return $result;
    }

    /**
     * SELECT
     * `r`.`id`,
     * `r`.`date_changed` AS `dateChanged`,
     * (CONCAT(c.firstname,
     * " ",
     * c.lastname)) AS `custName`,
     * `d`.`mlm_id` AS `custMlmId`,
     * `gc`.`customer_group_code` AS `groupCur`,
     * `go`.`customer_group_code` AS `groupOld`,
     * `gn`.`customer_group_code` AS `groupNew`
     * FROM
     * `prxgt_dwnl_change_group` AS `r`
     * LEFT JOIN `customer_entity` AS `c` ON
     * c.entity_id = r.customer_ref
     * LEFT JOIN `prxgt_dwnl_customer` AS `d` ON
     * d.customer_ref = r.customer_ref
     * LEFT JOIN `customer_group` AS `gc` ON
     * gc.customer_group_id = c.group_id
     * LEFT JOIN `customer_group` AS `go` ON
     * go.customer_group_id = r.group_old
     * LEFT JOIN `customer_group` AS `gn` ON
     * gn.customer_group_id = r.group_new
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function getQueryItems()
    {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCust = self::AS_CUST;
        $asDwnl = self::AS_DWNL;
        $asGrpCur = self::AS_GRP_CUR;
        $asGrpNew = self::AS_GRP_NEW;
        $asGrpOld = self::AS_GRP_OLD;
        $asReg = self::AS_REG;

        /* SELECT FROM prxgt_dwnl_change_group */
        $tbl = $this->resource->getTableName(self::E_REG);
        $as = $asReg;
        $cols = [
            self::A_ID => EChangeGroup::A_ID,
            self::A_CUST_ID => EChangeGroup::A_CUSTOMER_REF,
            self::A_DATE_CHANGED => EChangeGroup::A_DATE_CHANGED
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(self::E_CUST);
        $as = $asCust;
        $exp = $this->expCustName();
        $cols = [
            self::A_CUST_NAME => $exp
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asReg . '.' . EChangeGroup::A_CUSTOMER_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer */
        $tbl = $this->resource->getTableName(self::E_DWNL);
        $as = $asDwnl;
        $cols = [
            self::A_CUST_MLM_ID => EDwnlCust::A_MLM_ID
        ];
        $cond = $as . '.' . EDwnlCust::A_CUSTOMER_REF . '=' . $asReg . '.' . EChangeGroup::A_CUSTOMER_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_group as CUR */
        $tbl = $this->resource->getTableName(self::E_CUST_GROUP);
        $as = $asGrpCur;
        $cols = [
            self::A_GROUP_CUR => Cfg::E_CUSTGROUP_A_CODE
        ];
        $cond = $as . '.' . Cfg::E_CUSTGROUP_A_ID . '=' . $asCust . '.' . Cfg::E_CUSTOMER_A_GROUP_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_group as OLD */
        $tbl = $this->resource->getTableName(self::E_CUST_GROUP);
        $as = $asGrpOld;
        $cols = [
            self::A_GROUP_OLD => Cfg::E_CUSTGROUP_A_CODE
        ];
        $cond = $as . '.' . Cfg::E_CUSTGROUP_A_ID . '=' . $asReg . '.' . EChangeGroup::A_GROUP_OLD;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_group as NEW */
        $tbl = $this->resource->getTableName(self::E_CUST_GROUP);
        $as = $asGrpNew;
        $cols = [
            self::A_GROUP_NEW => Cfg::E_CUSTGROUP_A_CODE
        ];
        $cond = $as . '.' . Cfg::E_CUSTGROUP_A_ID . '=' . $asReg . '.' . EChangeGroup::A_GROUP_NEW;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* result */
        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_REG . '.' . EChangeGroup::A_ID . ')';

        /**
         * See method \Magento\Framework\DB\Select\ColumnsRenderer::render:
         */
        /**
         * if ($column instanceof \Zend_Db_Expr) {...}
         */
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($value);
        /**
         *  list($correlationName, $column, $alias) = $columnEntry;
         */
        $entry = [null, $exp, null];
        $cols = [$entry];
        $result->setPart('columns', $cols);
        return $result;
    }
}