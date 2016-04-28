<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Downline\Data\Entity\Change as Entity;
use Praxigento\Downline\Repo\Entity\IChange;

class Change extends BaseEntityRepo implements IChange
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

}