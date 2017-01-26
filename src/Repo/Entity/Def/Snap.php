<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Downline\Data\Entity\Snap as Entity;
use Praxigento\Downline\Repo\Entity\ISnap as IEntityRepo;

class Snap extends BaseEntityRepo implements IEntityRepo
{
    const AS_ATTR_DATE = 'date';

    /** @var \Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDate */
    protected $queryOnDate;
    /** @var \Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDateForDcp */
    protected $queryOnDateForDcp;
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric,
        \Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDate $queryOnDate,
        \Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDateForDcp $queryOnDateForDcp
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
        $this->queryOnDate = $queryOnDate;
        $this->queryOnDateForDcp = $queryOnDateForDcp;
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
     *
     * @return array
     */
    public function getStateOnDate($datestamp)
    {
        $result = [];
        $bind = [];
        $bind[\Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDate::BIND_DATE] = $datestamp;
        $query = $this->queryOnDate->getSelectQuery();
        $rows = $this->conn->fetchAll($query, $bind);
        if (count($rows)) {
            foreach ($rows as $one) {
                $result[$one[Entity::ATTR_CUSTOMER_ID]] = $one;
            }
        }
        return $result;
    }

    public function getStateOnDateExtended($datestamp)
    {
        $result = [];
        $bind = [];
        $bind[\Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDate::BIND_DATE] = $datestamp;
        $query = $this->queryOnDateForDcp->getSelectQuery();
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
                $this->create($data);
            }
        }
    }
}