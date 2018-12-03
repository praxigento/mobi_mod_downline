<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Dao;

use Praxigento\Downline\Repo\Data\Change as EChange;

class Change
    extends \Praxigento\Core\App\Repo\Dao
{
    /** see MOBI-1076 */
    const DATE_MIN = '1900-01-01 00:00:00';


    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
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
     * @return string|null
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

}