<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Snap extends EntityBase
{
    const ATTR_CUSTOMER_ID = 'customer_id';
    const ATTR_DATE = 'date';
    const ATTR_DEPTH = 'depth';
    const ATTR_PARENT_ID = 'parent_id';
    const ATTR_PATH = 'path';
    const ENTITY_NAME = 'prxgt_dwnl_snap';

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_DATE, self::ATTR_CUSTOMER_ID];
    }
}