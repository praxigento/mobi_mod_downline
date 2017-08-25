<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg\Def\Account;

use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Downline\Data\Agg\Account as AggEntity;
use Praxigento\Downline\Data\Entity\Customer as ECustomer;
use Praxigento\Downline\Repo\Agg\IAccount as AggRepo;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class SelectFactory
    extends \Praxigento\Accounting\Repo\Agg\Def\Account\SelectFactory
{

    private function _populateSelect(\Magento\Framework\DB\Select $select)
    {
        /* aliases and tables */
        $asAcc = AggRepo::AS_ACCOUNT;
        $asDwnl = AggRepo::AS_DOWNLINE;
        //
        $tblDwnl = [$asDwnl => $this->_resource->getTableName(ECustomer::ENTITY_NAME)];
        /* LEFT JOIN prxgt_dwnl_customer */
        $cond = $asDwnl . '.' . ECustomer::ATTR_CUSTOMER_ID . '=' . $asAcc . '.' . EAccount::ATTR_CUST_ID;
        $cols = [
            AggEntity::AS_REF => ECustomer::ATTR_HUMAN_REF
        ];
        $select->joinLeft($tblDwnl, $cond, $cols);
        return $select;
    }

    public function getQueryToSelect()
    {
        $result = parent::getQueryToSelect();
        $this->_populateSelect($result);
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = parent::getQueryToSelectCount();
        $this->_populateSelect($result);
        return $result;
    }
}