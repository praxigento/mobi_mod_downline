<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Customer extends EntityBase
{
    const ATTR_COUNTRY_CODE = 'country_code';
    const ATTR_CUSTOMER_ID = 'customer_id';
    const ATTR_DEPTH = 'depth';
    const ATTR_HUMAN_REF = 'human_ref';
    const ATTR_PARENT_ID = 'parent_id';
    const ATTR_PATH = 'path';
    const ENTITY_NAME = 'prxgt_dwnl_customer';

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_CUSTOMER_ID];
    }
}