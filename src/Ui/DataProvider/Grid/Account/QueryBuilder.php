<?php
/**
 * Created by PhpStorm.
 * User: dm
 * Date: 07.09.17
 * Time: 9:57
 */

namespace Praxigento\Downline\Ui\DataProvider\Grid\Account;

use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Downline\Repo\Entity\Data\Customer as EDownline;

class QueryBuilder
    extends \Praxigento\Accounting\Ui\DataProvider\Grid\Account\QueryBuilder
{

    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_DWNL = 'dwnl';
    /**#@- */

    /**#@+ Columns/expressions aliases for external usage */
    const A_MLMID = 'mlmId';

    /**#@- */


    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            /* init parent mapper */
            $this->mapper = parent::getMapper();
            /* then add own aliases */
            $key = self::A_MLMID;
            $value = self::AS_DWNL . '.' . EDownline::ATTR_HUMAN_REF;
            $this->mapper->add($key, $value);
        }
        $result = $this->mapper;
        return $result;
    }

    /**
     * SELECT
     * `paa`.`id`,
     * `paa`.`balance`,
     * `prxgt_acc_type_asset`.`code` AS `asset`,
     * (CONCAT(firstname, ' ', lastname)) AS `custName`,
     * `ce`.`email` AS `custEmail`
     * FROM `prxgt_acc_account` AS `paa`
     * LEFT JOIN `prxgt_acc_type_asset`
     * ON pata.id = paa.asset_type_id
     * LEFT JOIN `customer_entity` AS `ce`
     * ON ce.entity_id = paa.customer_id
     *
     * @inheritdoc
     */
    protected function getQueryItems()
    {
        /* this is primary query builder, not extender */
        $result = parent::getQueryItems();

        /* define tables aliases for internal usage (in this method) */
        $asDwnl = self::AS_DWNL;
        $asAcc = self::AS_ACCOUNT;

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(EDownline::ENTITY_NAME);
        $as = $asDwnl;
        $cols = [
            self::A_MLMID => EDownline::ATTR_HUMAN_REF
        ];
        $cond = $as . '.' . EDownline::ATTR_CUSTOMER_ID . '=' . $asAcc . '.' . EAccount::ATTR_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* return  result */
        return $result;
    }
}