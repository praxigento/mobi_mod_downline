<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Def;

use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Snap;
use Praxigento\Downline\Repo\IModule;

class Module implements IModule
{
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
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
        $tblChange = $this->_conn->getTableName(Change::ENTITY_NAME);
        /* select from account */
        $query = $this->_conn->select();
        $query->from([$asChange => $tblChange], [Change::ATTR_DATE_CHANGED]);
        /* order by */
        $query->order([$asChange . '.' . Change::ATTR_DATE_CHANGED . ' ASC']);
        /* perform query */
        $result = $this->_conn->fetchOne($query);
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
        $tblChange = $this->_conn->getTableName(Change::ENTITY_NAME);
        /* select from prxgt_dwnl_change */
        $query = $this->_conn->select();
        $query->from([$asChange => $tblChange]);
        /* where */
        $query->where($asChange . '.' . Change::ATTR_DATE_CHANGED . '>=:date_from');
        $query->where($asChange . '.' . Change::ATTR_DATE_CHANGED . '<=:date_to');
        $bind = [
            'date_from' => $timestampFrom,
            'date_to' => $timestampTo
        ];
        /**
         * Order by date changed, than by customer id (in tests date changed could be the same for all customers).
         * Order is important for tree snapshot calculation (MOBI-202)
         */
        $query->order([
            $asChange . '.' . Change::ATTR_DATE_CHANGED . ' ASC',
            $asChange . '.' . Change::ATTR_CUSTOMER_ID . ' ASC'
        ]);
        $result = $this->_conn->fetchAll($query, $bind);
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
    public function getSnapMaxDatestamp()
    {
        $result = null;
        $asSnap = 's';
        $tblSnap = $this->_conn->getTableName(Snap::ENTITY_NAME);
        /* select from account */
        $query = $this->_conn->select();
        $query->from([$asSnap => $tblSnap], [Snap::ATTR_DATE]);
        /* order by */
        $query->order([$asSnap . '.' . Snap::ATTR_DATE . ' DESC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->_conn->fetchOne($query);
        return $result;
    }


}