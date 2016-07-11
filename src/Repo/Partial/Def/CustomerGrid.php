<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Partial\Def;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Repo\Partial\ICustomerGrid;

class CustomerGrid implements ICustomerGrid
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
    }

    protected function _replaceAliaseInWhere($where, $fieldAlias, $tableAlias, $fieldName)
    {
        $search = "`$fieldAlias`";
        $replace = "`$tableAlias`.`$fieldName`";
        $result = str_replace($search, $replace, $where);
        return $result;
    }

    /**
     * @param array $where
     */
    protected function _replaceAllAliasesInWhere($where)
    {
        $result = [];
        foreach ($where as $item) {
            $item = $this->_replaceAliaseInWhere($item, self::AS_FLD_CUSTOMER_DEPTH, self::AS_TBL_CUST,
                Customer::ATTR_DEPTH);
            $item = $this->_replaceAliaseInWhere($item, self::AS_FLD_CUSTOMER_REF, self::AS_TBL_CUST,
                Customer::ATTR_HUMAN_REF);
            $item = $this->_replaceAliaseInWhere($item, self::AS_FLD_PARENT_ID, self::AS_TBL_CUST,
                Customer::ATTR_PARENT_ID);
            $item = $this->_replaceAliaseInWhere($item, self::AS_FLD_PARENT_REF, self::AS_TBL_PARENT_CUST,
                Customer::ATTR_HUMAN_REF);
            $result[] = $item;
        }
        return $result;
    }

    /** @inheritdoc */
    public function populateSelect($query)
    {
        $sql = (string)$query;
        /* LEFT JOIN `prxgt_dwnl_customer` AS `prxgtDwnlCust` */
        $tbl = [self::AS_TBL_CUST => $this->_resource->getTableName(Customer::ENTITY_NAME)];
        $on = self::AS_TBL_CUST . '.' . Customer::ATTR_CUSTOMER_ID . '=main_table.' . Cfg::E_CUSTOMER_A_ENTITY_ID;
        $cols = [
            self::AS_FLD_CUSTOMER_REF => Customer::ATTR_HUMAN_REF,
            self::AS_FLD_CUSTOMER_DEPTH => Customer::ATTR_DEPTH,
            self::AS_FLD_PARENT_ID => Customer::ATTR_PARENT_ID
        ];
        $query->joinLeft($tbl, $on, $cols);
        /* LEFT JOIN `prxgt_dwnl_customer` AS `prxgtDwnlParentCust` */
        $tbl = [self::AS_TBL_PARENT_CUST => $this->_resource->getTableName(Customer::ENTITY_NAME)];
        $on = self::AS_TBL_PARENT_CUST . '.' . Customer::ATTR_CUSTOMER_ID . '=' . self::AS_TBL_CUST . '.' . Customer::ATTR_PARENT_ID;
        $cols = [
            self::AS_FLD_PARENT_REF => Customer::ATTR_HUMAN_REF
        ];
        $query->joinLeft($tbl, $on, $cols);
        $sql = (string)$query;
        /* process WHERE part */
        $where = $query->getPart('where');
        $replaced = $this->_replaceAllAliasesInWhere($where);
        $query->setPart('where', $replaced);
        return $query;
    }

    /** @inheritdoc */
    public function populateSelectCount($query)
    {
        return $query;
    }
}