<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap\Sub;


use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Snap;

class Db extends \Praxigento\Core\Lib\Service\Base\Sub\Db {

    /**
     *Select MAX datestamp for downline snapshots.
     *
     * SELECT
     * `s`.`date`
     * FROM `prxgt_dwnl_snap` AS `s`
     * ORDER BY `s`.`date` DESC
     *
     * @return null|string YYYYMMDD
     *
     */
    public function getSnapMaxDatestamp() {
        $result = null;
        $asSnap = 's';
        $tblSnap = $this->_getTableName(Snap::ENTITY_NAME);
        /* select from account */
        $query = $this->_getConn()->select();
        $query->from([ $asSnap => $tblSnap ], [ Snap::ATTR_DATE ]);
        /* order by */
        $query->order([ $asSnap . '.' . Snap::ATTR_DATE . ' DESC' ]);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->_getConn()->fetchOne($query);
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
    public function getChangelogMinDate() {
        $result = null;
        $asChange = 'c';
        $tblChange = $this->_getTableName(Change::ENTITY_NAME);
        /* select from account */
        $query = $this->_getConn()->select();
        $query->from([ $asChange => $tblChange ], [ Change::ATTR_DATE_CHANGED ]);
        /* order by */
        $query->order([ $asChange . '.' . Change::ATTR_DATE_CHANGED . ' ASC' ]);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->_getConn()->fetchOne($query);
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
    public function getStateOnDate($datestamp) {
        $result = [ ];
        $bind = [ ];
        $asSnap4Max = 'snap4Max';
        $asSnap = 'snap';
        $asMax = 'snapMax';
        $tblSnap = $this->_getTableName(Snap::ENTITY_NAME);
        /* select MAX(date) from prxgt_dwnl_snap (internal select) */
        $q4Max = $this->_getConn()->select();
        $colDateMax = 'date_max';
        $expMaxDate = new \Zend_Db_Expr('MAX(`' . $asSnap4Max . '`.`' . Snap::ATTR_DATE . '`) as ' . $colDateMax);
        $q4Max->from([ $asSnap4Max => $tblSnap ], [ Snap::ATTR_CUSTOMER_ID, $expMaxDate ]);
        $q4Max->group($asSnap4Max . '.' . Snap::ATTR_CUSTOMER_ID);
        $q4Max->where($asSnap4Max . '.' . Snap::ATTR_DATE . '<=:date');
        $bind['date'] = $datestamp;
        /* select from prxgt_dwnl_snap */
        $query = $this->_getConn()->select();
        $query->from([ $asSnap => $tblSnap ], [
            Snap::ATTR_CUSTOMER_ID,
            Snap::ATTR_PARENT_ID,
            Snap::ATTR_DEPTH,
            Snap::ATTR_PATH
        ]);
        /* left join $q4Max */
        $on = '(' . $asMax . '.' . Snap::ATTR_CUSTOMER_ID . '=' . $asSnap . '.' . Snap::ATTR_CUSTOMER_ID . ')';
        $on .= ' AND (' . $asMax . '.' . $colDateMax . '=' . $asSnap . '.' . Snap::ATTR_DATE . ')';
        $query->joinLeft([ $asMax => $q4Max ], $on, [ ]);
        /* where */
        $query->where($asMax . '.' . $colDateMax . ' IS NOT NULL');
        // $sql = (string)$query;
        $rows = $this->_getConn()->fetchAll($query, $bind);
        if(count($rows)) {
            foreach($rows as $one) {
                $result[$one[Snap::ATTR_CUSTOMER_ID]] = $one;
            }
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
     * @return array
     */
    public function getChangesForPeriod($timestampFrom, $timestampTo) {
        $asChange = 'log';
        $tblChange = $this->_getTableName(Change::ENTITY_NAME);
        /* select from prxgt_dwnl_change */
        $query = $this->_getConn()->select();
        $query->from([ $asChange => $tblChange ]);
        /* where */
        $query->where($asChange . '.' . Change::ATTR_DATE_CHANGED . '>=:date_from');
        $query->where($asChange . '.' . Change::ATTR_DATE_CHANGED . '<=:date_to');
        $bind = [
            'date_from' => $timestampFrom,
            'date_to'   => $timestampTo
        ];
        /**
         * Order by date changed, than by customer id (in tests date changed could be the same for all customers).
         * Order is important for tree snapshot calculation (MOBI-202)
        */
        $query->order([
            $asChange . '.' . Change::ATTR_DATE_CHANGED . ' ASC',
            $asChange . '.' . Change::ATTR_CUSTOMER_ID . ' ASC'
        ]);
        // $sql = (string)$query;
        $result = $this->_getConn()->fetchAll($query, $bind);
        return $result;
    }

    /**
     * Insert snapshot updates. $updates is array [date][customerId] => $data
     *
     * @param $updates
     */
    public function saveCalculatedUpdates($updates) {
        $tbl = $this->_getTableName(Snap::ENTITY_NAME);
        foreach($updates as $date => $updatesByDate) {
            foreach($updatesByDate as $data) {
                $this->_getConn()->insert($tbl, $data);
            }
        }
    }
}