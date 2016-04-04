<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Change extends EntityBase
{
    const ATTR_CUSTOMER_ID = 'customer_id';
    const ATTR_DATE_CHANGED = 'date_changed';
    const ATTR_ID = 'id';
    const ATTR_PARENT_ID = 'parent_id';
    const ENTITY_NAME = 'prxgt_dwnl_change';

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
    }
}