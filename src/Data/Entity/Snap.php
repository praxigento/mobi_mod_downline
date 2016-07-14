<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Data\Entity;

class Snap
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_CUSTOMER_ID = 'customer_id';
    const ATTR_DATE = 'date';
    const ATTR_DEPTH = 'depth';
    const ATTR_PARENT_ID = 'parent_id';
    const ATTR_PATH = 'path';
    const ENTITY_NAME = 'prxgt_dwnl_snap';

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::getData(self::ATTR_CUSTOMER_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        $result = parent::getData(self::ATTR_DATE);
        return $result;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        $result = parent::getData(self::ATTR_DEPTH);
        return $result;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        $result = parent::getData(self::ATTR_PARENT_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $result = parent::getData(self::ATTR_PATH);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_DATE, self::ATTR_CUSTOMER_ID];
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::setData(self::ATTR_CUSTOMER_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setDate($data)
    {
        parent::setData(self::ATTR_DATE, $data);
    }

    /**
     * @param int $data
     */
    public function setDepth($data)
    {
        parent::setData(self::ATTR_DEPTH, $data);
    }

    /**
     * @param int $data
     */
    public function setParentId($data)
    {
        parent::setData(self::ATTR_PARENT_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setPath($data)
    {
        parent::setData(self::ATTR_PATH, $data);
    }
}