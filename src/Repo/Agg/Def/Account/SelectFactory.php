<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg\Def\Account;

use Praxigento\Accounting\Data\Entity\Account as EntityAccount;
use Praxigento\Downline\Data\Agg\Account as AggEntity;
use Praxigento\Downline\Data\Entity\Customer as EntityCustomer;
use Praxigento\Downline\Repo\Agg\IAccount as AggRepo;

class SelectFactory
    extends \Praxigento\Accounting\Repo\Agg\Def\Account\SelectFactory
{

    private function _populateSelect(\Magento\Framework\DB\Select $select)
    {
        /* aliases and tables */
        $asAcc = AggRepo::AS_ACCOUNT;
        $asDwnl = AggRepo::AS_DOWNLINE;
        //
        $tblDwnl = [$asDwnl => $this->_resource->getTableName(EntityCustomer::ENTITY_NAME)];
        /* LEFT JOIN prxgt_dwnl_customer */
        $on = $asDwnl . '.' . EntityCustomer::ATTR_CUSTOMER_ID . '=' . $asAcc . '.' . EntityAccount::ATTR_CUST_ID;
        $cols = [
            AggEntity::AS_REF => EntityCustomer::ATTR_HUMAN_REF
        ];
        $select->joinLeft($tblDwnl, $on, $cols);
        return $select;
    }

    /** @inheritdoc */
    public function getQueryToSelectCount()
    {
        $result = parent::getQueryToSelectCount();
        $this->_populateSelect($result);
        return $result;
    }

    /** @inheritdoc */
    public function getQueryToSelect()
    {
        $result = parent::getQueryToSelect();
        $this->_populateSelect($result);
        return $result;
    }
}