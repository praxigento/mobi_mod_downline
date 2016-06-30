<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity;

use Praxigento\Core\Repo\IBaseCrud;
use Praxigento\Downline\Data\Entity\Snap as Entity;

interface ISnap extends IBaseCrud
{
    /**
     * @param array|Entity $data
     * @return int
     */
    public function create($data);

    /**
     * @param int $id
     * @return Entity|bool
     */
    public function getById($id);

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
     * @param $datestamp string 'YYYYMMDD'
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