<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def\Snap\Query;

/**
 * Query to get downline tree snap on given date with additional attributes for DCP.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class OnDateForDcp
{
    const AS_ATTR_COUNTRY_CODE = \Praxigento\Downline\Data\Entity\Customer::ATTR_COUNTRY_CODE;
    const AS_ATTR_EMAIL = 'email';
    const AS_ATTR_MLM_ID = 'mlm_id';
    const AS_ATTR_NAME_FIRST = 'name_first';
    const AS_ATTR_NAME_LAST = 'name_last';
    const AS_TBL_DOWNLINE_CUSTOMER = 'prxgtDwnlCust';
    const BIND_DATE = 'date';
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDate */
    protected $queryOnDate;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDate $queryOnDate
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
        $asDwnlSnap = \Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDate::AS_TBL_DWNL_SNAP;
        $tblDwnlCust = [
            $asDwnlCust => $this->resource->getTableName(\Praxigento\Downline\Data\Entity\Customer::ENTITY_NAME)
        ];
        /* LEFT JOIN prxgt_dwnl_customer pdc */
        $on = $asDwnlCust . '.' . \Praxigento\Downline\Data\Entity\Customer::ATTR_CUSTOMER_ID . '='
            . $asDwnlSnap . '.' . \Praxigento\Downline\Data\Entity\Snap::ATTR_CUSTOMER_ID;
        $cols = [
            self::AS_ATTR_MLM_ID => \Praxigento\Downline\Data\Entity\Customer::ATTR_HUMAN_REF,
            self::AS_ATTR_COUNTRY_CODE => \Praxigento\Downline\Data\Entity\Customer::ATTR_COUNTRY_CODE
        ];
        $result->joinLeft($tblDwnlCust, $on, $cols);
        return $result;
    }
}