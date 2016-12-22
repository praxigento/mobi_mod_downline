<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Data\Entity;

class Change
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_CUSTOMER_ID = 'customer_id';
    const ATTR_DATE_CHANGED = 'date_changed';
    const ATTR_ID = 'id';
    const ATTR_PARENT_ID = 'parent_id';
    const ENTITY_NAME = 'prxgt_dwnl_change';

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::get(self::ATTR_CUSTOMER_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getDateChanged()
    {
        $result = parent::get(self::ATTR_DATE_CHANGED);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::get(self::ATTR_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        $result = parent::get(self::ATTR_PARENT_ID);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::set(self::ATTR_CUSTOMER_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setDateChanged($data)
    {
        parent::set(self::ATTR_DATE_CHANGED, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::set(self::ATTR_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setParentId($data)
    {
        parent::set(self::ATTR_PARENT_ID, $data);
    }
}