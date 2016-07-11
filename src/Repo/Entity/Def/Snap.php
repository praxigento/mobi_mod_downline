<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Core\Repo\Query\Expression;
use Praxigento\Downline\Data\Entity\Snap as Entity;
use Praxigento\Downline\Repo\Entity\ISnap as IEntityRepo;

class Snap extends BaseEntityRepo implements IEntityRepo
{

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
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
        $tblSnap = $this->_resource->getTableName(Entity::ENTITY_NAME);
        /* select from account */
        $query = $this->_conn->select();
        $query->from([$asSnap => $tblSnap], [Entity::ATTR_DATE]);
        /* order by */
        $query->order([$asSnap . '.' . Entity::ATTR_DATE . ' DESC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->_conn->fetchOne($query);
        return $result;
    }

    /**
     * Select downline tree state on the given datestamp.
     *
     * SELECT
     * `snap`.`customer_id`,
     * `snap`.`parent_id`,
     * `snap`.`depth`,
     * `snap`.`path`
     * FROM `prxgt_dwnl_snap` AS `snap`
     * LEFT JOIN (SELECT
     * `snap4Max`.`customer_id`,
     * MAX(`snap4Max`.`date`) AS date_max
     * FROM `prxgt_dwnl_snap` AS `snap4Max`
     * WHERE (snap4Max.date <= :date)
     * GROUP BY `snap4Max`.`customer_id`) AS `snapMax`
     * ON (snapMax.customer_id = snap.customer_id)
     * AND (snapMax.date_max = snap.date)
     * WHERE (snapMax.date_max IS NOT NULL)
     *
     * @param $datestamp string 'YYYYMMDD'
     *
     * @return array
     */
    public function getStateOnDate($datestamp)
    {
        $result = [];
        $bind = [];
        $asSnap4Max = 'snap4Max';
        $asSnap = 'snap';
        $asMax = 'snapMax';
        $tblSnap = $this->_resource->getTableName(Entity::ENTITY_NAME);
        /* select MAX(date) from prxgt_dwnl_snap (internal select) */
        $q4Max = $this->_conn->select();
        $colDateMax = 'date_max';
        $expMaxDate = new Expression('MAX(`' . $asSnap4Max . '`.`' . Entity::ATTR_DATE . '`)');
        $q4Max->from([$asSnap4Max => $tblSnap], [Entity::ATTR_CUSTOMER_ID, $colDateMax => $expMaxDate]);
        $q4Max->group($asSnap4Max . '.' . Entity::ATTR_CUSTOMER_ID);
        $q4Max->where($asSnap4Max . '.' . Entity::ATTR_DATE . '<=:date');
        $bind['date'] = $datestamp;
        /* select from prxgt_dwnl_snap */
        $query = $this->_conn->select();
        $query->from([$asSnap => $tblSnap], [
            Entity::ATTR_CUSTOMER_ID,
            Entity::ATTR_PARENT_ID,
            Entity::ATTR_DEPTH,
            Entity::ATTR_PATH
        ]);
        /* left join $q4Max */
        $on = '(' . $asMax . '.' . Entity::ATTR_CUSTOMER_ID . '=' . $asSnap . '.' . Entity::ATTR_CUSTOMER_ID . ')';
        $on .= ' AND (' . $asMax . '.' . $colDateMax . '=' . $asSnap . '.' . Entity::ATTR_DATE . ')';
        $query->joinLeft([$asMax => $q4Max], $on, []);
        /* where */
        $query->where($asMax . '.' . $colDateMax . ' IS NOT NULL');
        // $sql = (string)$query;
        $rows = $this->_conn->fetchAll($query, $bind);
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