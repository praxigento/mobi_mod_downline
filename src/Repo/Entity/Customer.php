<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Entity;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Downline\Repo\Entity\Data\Customer as Entity;
use Praxigento\Downline\Repo\Entity\Delta;

class Customer extends BaseEntityRepo
{

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    )
    {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * @param \Praxigento\Downline\Repo\Entity\Data\Customer|array $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Downline\Repo\Entity\Data\Customer|bool
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
     * @return \Praxigento\Downline\Repo\Entity\Data\Customer|null
     */
    public function getByMlmId($mlmId)
    {
        $result = null;
        $cols = null;
        $qCode = $this->conn->quote($mlmId);
        $where = Entity::ATTR_HUMAN_REF . '=' . $qCode;
        $items = $this->repoGeneric->getEntities(Entity::ENTITY_NAME, $cols, $where);
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
     * @return \Praxigento\Downline\Repo\Entity\Data\Customer|null
     */
    public function getByReferralCode($code)
    {
        $result = null;
        $cols = null;
        $qCode = $this->conn->quote($code);
        $where = Entity::ATTR_REFERRAL_CODE . '=' . $qCode;
        $items = $this->repoGeneric->getEntities(Entity::ENTITY_NAME, $cols, $where);
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
            Entity::ATTR_DEPTH . '+' . abs($depthDelta) :
            Entity::ATTR_DEPTH . '-' . abs($depthDelta);
        $sqlPath = 'REPLACE(' . Entity::ATTR_PATH . ", $qPath, $qReplace)";
        $bind = [
            Entity::ATTR_DEPTH => $sqlDepth,
            Entity::ATTR_PATH => $sqlPath
        ];
        $where = Entity::ATTR_PATH . " LIKE $qPathMask";
        $result = $this->repoGeneric->updateEntity(Entity::ENTITY_NAME, $bind, $where);
        return $result;
    }
}