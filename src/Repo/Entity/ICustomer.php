<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity;

use Praxigento\Core\Repo\ICrud;
use Praxigento\Downline\Data\Entity\Customer as Entity;

interface ICustomer extends ICrud
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
     * @param string $code
     * @return Entity|null
     */
    public function getByReferralCode($code);

    /**
     * Replace path for all children ('/1/3/6/%' => '/1/2/5/6/%') and depth.
     *
     * @param string $path Path to search and replace (/1/3/6/)
     * @param string $replace Replacement path (/1/2/5/6/)
     * @param $depthDelta Delta for depth changes
     * @return int number of the changed rows
     */
    public function updateChildrenPath($path, $replace, $depthDelta);

}