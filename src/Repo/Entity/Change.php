<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Entity;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Downline\Repo\Entity\Data\Change as Entity;

class Change extends BaseEntityRepo
{

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    )
    {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * @param array|\Praxigento\Downline\Repo\Entity\Data\Change $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Downline\Repo\Entity\Data\Change|bool
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

    /**
     * Select MIN date for the existing change log.
     *
     * SELECT
     * `c`.`date_changed`
     * FROM `prxgt_dwnl_change` AS `c`
     * ORDER BY `c`.`date_changed` ASC
     *
     * @return null|string
     */
    public function getChangelogMinDate()
    {
        $result = null;
        $asChange = 'c';
        $tblChange = $this->resource->getTableName(Entity::ENTITY_NAME);
        /* select from account */
        $query = $this->conn->select();
        $query->from([$asChange => $tblChange], [Entity::ATTR_DATE_CHANGED]);
        /* order by */
        $query->order([$asChange . '.' . Entity::ATTR_DATE_CHANGED . ' ASC']);
        /* perform query */
        $result = $this->conn->fetchOne($query);
        return $result;
    }

    /**
     * SELECT
     * `log`.*
     * FROM `prxgt_dwnl_change` AS `log`
     * WHERE
     * (log.date_changed >= :date_from) AND
     * (log.date_changed <= :date_to)
     * ORDER BY `log`.`date_changed` ASC
     *
     * @param $timestampFrom
     * @param $timestampTo
     *
     * @return array
     */
    public function getChangesForPeriod($timestampFrom, $timestampTo)
    {
        $asChange = 'log';
        $tblChange = $this->resource->getTableName(Entity::ENTITY_NAME);
        /* select from prxgt_dwnl_change */
        $query = $this->conn->select();
        $query->from([$asChange => $tblChange]);
        /* where */
        $query->where($asChange . '.' . Entity::ATTR_DATE_CHANGED . '>=:date_from');
        $query->where($asChange . '.' . Entity::ATTR_DATE_CHANGED . '<=:date_to');
        $bind = [
            'date_from' => $timestampFrom,
            'date_to' => $timestampTo
        ];
        /**
         * Order by date changed, than by customer id (in tests date changed could be the same for all customers).
         * Order is important for tree snapshot calculation (MOBI-202)
         */
        $query->order([
            $asChange . '.' . Entity::ATTR_DATE_CHANGED . ' ASC',
            $asChange . '.' . Entity::ATTR_CUSTOMER_ID . ' ASC'
        ]);
        $result = $this->conn->fetchAll($query, $bind);
        return $result;
    }

}