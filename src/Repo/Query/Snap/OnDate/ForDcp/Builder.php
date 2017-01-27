<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp;

use Praxigento\Downline\Config as Cfg;

/**
 * Build query to get downline tree snap on given date with additional attributes for DCP.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Builder
{
    const AS_ATTR_COUNTRY_CODE = \Praxigento\Downline\Data\Entity\Customer::ATTR_COUNTRY_CODE;
    const AS_ATTR_EMAIL = Cfg::E_CUSTOMER_A_EMAIL;
    const AS_ATTR_MLM_ID = 'mlm_id';
    const AS_ATTR_NAME_FIRST = 'name_first';
    const AS_ATTR_NAME_LAST = 'name_last';
    const AS_TBL_DOWNLINE_CUSTOMER = 'prxgtDwnlCust';
    const AS_TBL_CUSTOMER = 'mageCust';
    const BIND_DATE = 'date';
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder */
    protected $queryOnDate;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder $queryOnDate
    ) {
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->queryOnDate = $queryOnDate;
    }

    public function getCountQuery()
    {
        throw  new \Exception("Is not implemented yet.");
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectQuery()
    {
        $result = $this->queryOnDate->getSelectQuery();
        $asDwnlCust = self::AS_TBL_DOWNLINE_CUSTOMER;
        $asDwnlSnap = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_TBL_DWNL_SNAP;
        $asCust = self::AS_TBL_CUSTOMER;
        /* LEFT JOIN prxgt_dwnl_customer pdc */
        $tblDwnlCust = [
            $asDwnlCust => $this->resource->getTableName(\Praxigento\Downline\Data\Entity\Customer::ENTITY_NAME)
        ];
        $on = $asDwnlCust . '.' . \Praxigento\Downline\Data\Entity\Customer::ATTR_CUSTOMER_ID . '='
            . $asDwnlSnap . '.' . \Praxigento\Downline\Data\Entity\Snap::ATTR_CUSTOMER_ID;
        $cols = [
            self::AS_ATTR_MLM_ID => \Praxigento\Downline\Data\Entity\Customer::ATTR_HUMAN_REF,
            self::AS_ATTR_COUNTRY_CODE => \Praxigento\Downline\Data\Entity\Customer::ATTR_COUNTRY_CODE
        ];
        $result->joinLeft($tblDwnlCust, $on, $cols);
        /* LEFT JOIN customer_entity ce */
        $tblCust = [
            $asCust => $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)
        ];
        $on = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '='
            . $asDwnlCust . '.' . \Praxigento\Downline\Data\Entity\Customer::ATTR_CUSTOMER_ID;
        $cols = [
            self::AS_ATTR_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::AS_ATTR_NAME_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::AS_ATTR_NAME_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $result->joinLeft($tblCust, $on, $cols);
        return $result;
    }
}