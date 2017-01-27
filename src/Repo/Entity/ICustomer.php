<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity;

interface ICustomer
    extends \Praxigento\Core\Repo\ICrud
{
    /**
     * @param \Praxigento\Downline\Data\Entity\Customer|array $data
     * @return int
     */
    public function create($data);

    /**
     * @param int $id
     * @return \Praxigento\Downline\Data\Entity\Customer|bool
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id);

    /**
     * @param string $code
     * @return \Praxigento\Downline\Data\Entity\Customer|null
     */
    public function getByReferralCode($code);

    /**
     * @param string $mlmId
     * @return \Praxigento\Downline\Data\Entity\Customer|null
     */
    public function getByMlmId($mlmId);

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