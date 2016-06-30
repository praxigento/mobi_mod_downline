<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity;

use Praxigento\Core\Repo\IBaseCrud;
use Praxigento\Downline\Data\Entity\Change as Entity;

interface IChange extends IBaseCrud
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

}