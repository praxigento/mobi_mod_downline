<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Dao;

use Praxigento\Downline\Repo\Data\Change as EChange;

class Change
    extends \Praxigento\Core\App\Repo\Def\Entity
{
    /** see MOBI-1076 */
    const DATE_MIN = '1900-01-01 00:00:00';


    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric
    ) {
        parent::__construct($resource, $daoGeneric, EChange::class);
    }

    /**
     * @param array|\Praxigento\Downline\Repo\Data\Change $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Downline\Repo\Data\Change|bool
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
        $tblChange = $this->resource->getTableName(EChange::ENTITY_NAME);
        /* select from account */
        $query = $this->conn->select();
        $query->from([$asChange => $tblChange], [EChange::A_DATE_CHANGED]);
        /* order by */
        $query->order([$asChange . '.' . EChange::A_DATE_CHANGED . ' ASC']);
        /* perform query */
        $result = $this->conn->fetchOne($query);
        if ($result < self::DATE_MIN) {
            $result = self::DATE_MIN;
        }
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
     * @return EChange[]
     */
    public function getChangesForPeriod($timestampFrom, $timestampTo)
    {
        $asChange = 'log';
        $tblChange = $this->resource->getTableName(EChange::ENTITY_NAME);
        /* select from prxgt_dwnl_change */
        $query = $this->conn->select();
        $query->from([$asChange => $tblChange]);
        /* where */
        $query->where($asChange . '.' . EChange::A_DATE_CHANGED . '>=:date_from');
        $query->where($asChange . '.' . EChange::A_DATE_CHANGED . '<:date_to');
        $bind = [
            'date_from' => $timestampFrom,
            'date_to' => $timestampTo
        ];
        /**
         * Order by date changed, than by customer id (in tests date changed could be the same for all customers).
         * Order is important for tree snapshot calculation (MOBI-202)
         */
        $query->order([
            $asChange . '.' . EChange::A_DATE_CHANGED . ' ASC',
            $asChange . '.' . EChange::A_CUSTOMER_ID . ' ASC'
        ]);
        $rs = $this->conn->fetchAll($query, $bind);
        $result = [];
        foreach ($rs as $one) {
            $item = new EChange($one);
            $result[] = $item;
        }
        return $result;
    }

}