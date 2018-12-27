<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Dao;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Api\App\Repo\Generic as IRepoGeneric;
use Praxigento\Downline\Repo\Data\Customer as ECustomer;
use Praxigento\Downline\Repo\Data\Snap as Entity;
use Praxigento\Downline\Repo\Query\Snap\OnDate\Builder as QBldSnap;
use Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder as QBldMax;

class Snap
    extends \Praxigento\Core\App\Repo\Dao
{
    const AS_A_DATE = 'date';
    const AS_TBL_DWNL = 'prxgtDwnlAct';
    const A_COUNTRY = ECustomer::A_COUNTRY_CODE;
    const A_MLM_ID = ECustomer::A_MLM_ID;

    /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder */
    protected $qbuildSnapOnDate;

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $daoGeneric,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder $qbuildSnapOnDate
    ) {
        parent::__construct($resource, $daoGeneric, Entity::class);
        $this->qbuildSnapOnDate = $qbuildSnapOnDate;
    }

    /**
     * @param array|\Praxigento\Downline\Repo\Data\Snap $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Downline\Repo\Data\Snap|bool
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

    /**
     * Select MAX datestamp for downline snapshots.
     *
     * SELECT
     * `s`.`date`
     * FROM `prxgt_dwnl_snap` AS `s`
     * ORDER BY `s`.`date` DESC
     *
     * @return string|null YYYYMMDD
     *
     */
    public function getMaxDatestamp()
    {
        $result = null;
        $asSnap = 's';
        $tblSnap = $this->resource->getTableName(Entity::ENTITY_NAME);
        /* select from account */
        $query = $this->conn->select();
        $query->from([$asSnap => $tblSnap], [Entity::A_DATE]);
        /* order by */
        $query->order([$asSnap . '.' . Entity::A_DATE . ' DESC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->conn->fetchOne($query);
        return $result;
    }

    /**
     * Select downline tree state on the given datestamp.
     *
     * @param $datestamp string 'YYYYMMDD'
     * @param $addCountryCode 'true' to add actual country code for customer's attributes
     *
     * @return array
     */
    public function getStateOnDate($datestamp, $addCountryCode = false)
    {
        $result = [];
        $bind = [];
        $bind[QBldMax::BND_ON_DATE] = $datestamp;
        $query = $this->qbuildSnapOnDate->build();
        if ($addCountryCode) {
            /* define tables aliases */
            $as = self::AS_TBL_DWNL;
            $tbl = $this->resource->getTableName(ECustomer::ENTITY_NAME);
            $on = $as . '.' . ECustomer::A_CUSTOMER_REF . '='
                . QBldSnap::AS_DWNL_SNAP . '.' . Entity::A_CUSTOMER_REF;
            $cols = [
                self::A_COUNTRY => ECustomer::A_COUNTRY_CODE,
                self::A_MLM_ID => ECustomer::A_MLM_ID
            ];
            $query->joinLeft([$as => $tbl], $on, $cols);
        }
        $query->order(
            QBldSnap::AS_DWNL_SNAP . '.'
            . \Praxigento\Downline\Repo\Data\Snap::A_DEPTH
        );
        $rows = $this->conn->fetchAll($query, $bind);
        if (count($rows)) {
            foreach ($rows as $one) {
                $result[$one[\Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::A_CUST_ID]] = $one;
            }
        }
        return $result;
    }

    /**
     * Insert snapshot updates. $updates is array [date][customerId] => $data
     *
     * @param $updates
     */
    public function saveCalculatedUpdates($updates)
    {
        foreach ($updates as $date => $updatesByDate) {
            /** @var Entity $data */
            foreach ($updatesByDate as $data) {
                /* some entries are without dates */
                $data->setDate($date);
                $this->replace($data);
            }
        }
    }
}