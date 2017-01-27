<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity;

use Praxigento\Core\Repo\ICrud;
use Praxigento\Downline\Data\Entity\Snap as Entity;

interface ISnap extends ICrud
{
    /**
     * @param array|Entity $data
     * @return int
     */
    public function create($data);

    /**
     * @param int $id
     * @return Entity|bool
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id);

    /**
     * Get customer data snapshort on date (less or equal to).
     *
     * @param int $id
     * @param string $datestamp 'YYYYMMDD'
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getByCustomerIdOnDate($id, $datestamp);

    /**
     * Select MAX datestamp for downline snapshots.
     *
     * @return null|string YYYYMMDD
     *
     */
    public function getMaxDatestamp();

    /**
     * Select downline tree state on the given datestamp.
     *
     * @param string $datestamp 'YYYYMMDD'
     *
     * @return array
     */
    public function getStateOnDate($datestamp);

    /**
     * Insert snapshot updates. $updates is array [date][customerId] => $data
     *
     * @param $updates
     */
    public function saveCalculatedUpdates($updates);
}