<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def\Snap\Query;

use Praxigento\Downline\Data\Entity\Snap as Entity;

/**
 * Query to get downline tree snap on given date.
 */
class OnDate
{
    const BIND_DATE = 'date';

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
    }

    /**
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
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectQuery()
    {
        $asSnap4Max = 'snap4Max';
        $asSnap = 'snap';
        $asMax = 'snapMax';
        $tblSnap = $this->resource->getTableName(Entity::ENTITY_NAME);
        /* select MAX(date) from prxgt_dwnl_snap (internal select) */
        $q4Max = $this->conn->select();
        $colDateMax = 'date_max';
        $expMaxDate = new \Praxigento\Core\Repo\Query\Expression(
            'MAX(`' . $asSnap4Max . '`.`' . Entity::ATTR_DATE . '`)'
        );
        $q4Max->from([$asSnap4Max => $tblSnap], [Entity::ATTR_CUSTOMER_ID, $colDateMax => $expMaxDate]);
        $q4Max->group($asSnap4Max . '.' . Entity::ATTR_CUSTOMER_ID);
        $q4Max->where($asSnap4Max . '.' . Entity::ATTR_DATE . '<=:' . self::BIND_DATE);
        /* select from prxgt_dwnl_snap */
        $result = $this->conn->select();
        $result->from([$asSnap => $tblSnap], [
            Entity::ATTR_CUSTOMER_ID,
            Entity::ATTR_PARENT_ID,
            Entity::ATTR_DEPTH,
            Entity::ATTR_PATH
        ]);
        /* left join $q4Max */
        $on = '(' . $asMax . '.' . Entity::ATTR_CUSTOMER_ID . '=' . $asSnap . '.' . Entity::ATTR_CUSTOMER_ID . ')';
        $on .= ' AND (' . $asMax . '.' . $colDateMax . '=' . $asSnap . '.' . Entity::ATTR_DATE . ')';
        $result->joinLeft([$asMax => $q4Max], $on, []);
        /* where */
        $result->where($asMax . '.' . $colDateMax . ' IS NOT NULL');
        return $result;
    }

    public function getCountQuery()
    {
        throw  new \Exception("Is not implemented yet.");
    }
}