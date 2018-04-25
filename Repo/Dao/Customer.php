<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Dao;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\App\Repo\Dao as BaseEntityRepo;
use Praxigento\Core\Api\App\Repo\Generic as IRepoGeneric;
use Praxigento\Core\App\Repo\Query\Expression as AnExpression;
use Praxigento\Downline\Repo\Data\Customer as Entity;

class Customer extends BaseEntityRepo
{

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $daoGeneric
    )
    {
        parent::__construct($resource, $daoGeneric, Entity::class);
    }

    /**
     * @param \Praxigento\Downline\Repo\Data\Customer|array $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Downline\Repo\Data\Customer|bool
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

    /**
     * @param string $mlmId
     * @return \Praxigento\Downline\Repo\Data\Customer|null
     */
    public function getByMlmId($mlmId)
    {
        $result = null;
        $cols = null;
        $qCode = $this->conn->quote($mlmId);
        $where = Entity::A_MLM_ID . '=' . $qCode;
        $items = $this->daoGeneric->getEntities(Entity::ENTITY_NAME, $cols, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $data = reset($items);
            $result = $this->createEntity($data);
        }
        return $result;
    }

    /**
     * @param string $code
     * @return \Praxigento\Downline\Repo\Data\Customer|null
     */
    public function getByReferralCode($code)
    {
        $result = null;
        $cols = null;
        $qCode = $this->conn->quote($code);
        $where = Entity::A_REFERRAL_CODE . '=' . $qCode;
        $items = $this->daoGeneric->getEntities(Entity::ENTITY_NAME, $cols, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $data = reset($items);
            $result = $this->createEntity($data);
        }
        return $result;
    }

    /**
     * Replace path for all children ('/1/3/6/%' => '/1/2/5/6/%') and depth.
     *
     * @param string $path Path to search and replace (/1/3/6/)
     * @param string $replace Replacement path (/1/2/5/6/)
     * @param $depthDelta Delta for depth changes
     * @return int number of the changed rows
     */
    public function updateChildrenPath($path, $replace, $depthDelta)
    {
        $qPath = $this->conn->quote($path);
        $qPathMask = $this->conn->quote($path . '%');
        $qReplace = $this->conn->quote($replace);
        $sqlDepth = ($depthDelta > 0) ?
            Entity::A_DEPTH . '+' . abs($depthDelta) :
            Entity::A_DEPTH . '-' . abs($depthDelta);
        $sqlPath = 'REPLACE(' . Entity::A_PATH . ", $qPath, $qReplace)";
        $bind = [
            Entity::A_DEPTH => new AnExpression($sqlDepth),
            Entity::A_PATH => new AnExpression($sqlPath)
        ];
        $where = Entity::A_PATH . " LIKE $qPathMask";
        $result = $this->daoGeneric->updateEntity(Entity::ENTITY_NAME, $bind, $where);
        return $result;
    }
}