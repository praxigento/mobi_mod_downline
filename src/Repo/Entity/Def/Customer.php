<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Downline\Data\Entity\Customer as Entity;
use Praxigento\Downline\Repo\Entity\Delta;
use Praxigento\Downline\Repo\Entity\ICustomer as IEntityRepo;

class Customer extends BaseEntityRepo implements IEntityRepo
{

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    public function getByReferralCode($code)
    {
        $result = null;
        $cols = null;
        $qCode = $this->_conn->quote($code);
        $where = Entity::ATTR_REFERRAL_CODE . '=' . $qCode;
        $items = $this->_repoGeneric->getEntities(Entity::ENTITY_NAME, $cols, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $data = reset($items);
            $result = $this->_createEntityInstance($data);
        }
        return $result;
    }

    public function getByMlmId($mlmId)
    {
        $result = null;
        $cols = null;
        $qCode = $this->_conn->quote($mlmId);
        $where = Entity::ATTR_HUMAN_REF . '=' . $qCode;
        $items = $this->_repoGeneric->getEntities(Entity::ENTITY_NAME, $cols, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $data = reset($items);
            $result = $this->_createEntityInstance($data);
        }
        return $result;
    }

    public function updateChildrenPath($path, $replace, $depthDelta)
    {
        $qPath = $this->_conn->quote($path);
        $qPathMask = $this->_conn->quote($path . '%');
        $qReplace = $this->_conn->quote($replace);
        $sqlDepth = ($depthDelta > 0) ?
            Entity::ATTR_DEPTH . '+' . abs($depthDelta) :
            Entity::ATTR_DEPTH . '-' . abs($depthDelta);
        $sqlPath = 'REPLACE(' . Entity::ATTR_PATH . ", $qPath, $qReplace)";
        $bind = [
            Entity::ATTR_DEPTH => $sqlDepth,
            Entity::ATTR_PATH => $sqlPath
        ];
        $where = Entity::ATTR_PATH . " LIKE $qPathMask";
        $result = $this->_repoGeneric->updateEntity(Entity::ENTITY_NAME, $bind, $where);
        return $result;
    }
}