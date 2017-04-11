<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Downline\Data\Entity\Customer as ECustomer;
use Praxigento\Downline\Data\Entity\Snap as Entity;
use Praxigento\Downline\Repo\Entity\ISnap as IEntityRepo;
use Praxigento\Downline\Repo\Query\Snap\OnDate\Builder as QBldSnap;
use Praxigento\Downline\Repo\Query\Snap\OnDate\Max\Builder as QBldMax;

class Snap extends BaseEntityRepo implements IEntityRepo
{
    const AS_ATTR_DATE = 'date';
    const AS_TBL_DWNL = 'prxgtDwnlAct';
    const A_COUNTRY = ECustomer::ATTR_COUNTRY_CODE;
    const A_MLM_ID = ECustomer::ATTR_HUMAN_REF;

    /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder */
    protected $qbuildSnapOnDate;

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder $qbuildSnapOnDate
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
        $this->qbuildSnapOnDate = $qbuildSnapOnDate;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getByCustomerIdOnDate($id, $datestamp)
    {
        $result = null;
        $tbl = $this->resource->getTableName(Entity::ENTITY_NAME);
        $query = $this->conn->select();
        $query->from($tbl);
        $bind = [];
        /* where */
        $where = Entity::ATTR_CUSTOMER_ID . '= :id';
        $bind['id'] = (int)$id;
        $query->where($where);
        $where = Entity::ATTR_DATE . '<= :date';
        $bind['date'] = $datestamp;
        $query->where($where);
        /* order by */
        $query->order(Entity::ATTR_DATE . ' DESC');
        /* get one only record */
        $query->limit(1);
        /* perform query */
        $result = $this->conn->fetchRow($query, $bind);
        if ($result) {
            $result = $this->_createEntityInstance($result);
        }
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
     * @return null|string YYYYMMDD
     *
     */
    public function getMaxDatestamp()
    {
        $result = null;
        $asSnap = 's';
        $tblSnap = $this->resource->getTableName(Entity::ENTITY_NAME);
        /* select from account */
        $query = $this->conn->select();
        $query->from([$asSnap => $tblSnap], [Entity::ATTR_DATE]);
        /* order by */
        $query->order([$asSnap . '.' . Entity::ATTR_DATE . ' DESC']);
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
        $bind[QBldMax::BIND_ON_DATE] = $datestamp;
        $query = $this->qbuildSnapOnDate->getSelectQuery();
        if ($addCountryCode) {
            /* define tables aliases */
            $as = self::AS_TBL_DWNL;
            $tbl = $this->resource->getTableName(ECustomer::ENTITY_NAME);
            $on = $as . '.' . ECustomer::ATTR_CUSTOMER_ID . '='
                . QBldSnap::AS_DWNL_SNAP . '.' . Entity::ATTR_CUSTOMER_ID;
            $cols = [
                self::A_COUNTRY => ECustomer::ATTR_COUNTRY_CODE,
                self::A_MLM_ID => ECustomer::ATTR_HUMAN_REF
            ];
            $query->joinLeft([$as => $tbl], $on, $cols);
        }
        $query->order(
            QBldSnap::AS_DWNL_SNAP . '.'
            . \Praxigento\Downline\Data\Entity\Snap::ATTR_DEPTH
        );
        $rows = $this->conn->fetchAll($query, $bind);
        if (count($rows)) {
            foreach ($rows as $one) {
                $result[$one[Entity::ATTR_CUSTOMER_ID]] = $one;
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
            foreach ($updatesByDate as $data) {
                $this->replace($data);
            }
        }
    }
}