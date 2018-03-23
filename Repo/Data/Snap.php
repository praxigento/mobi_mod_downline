<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Data;

class Snap
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CUSTOMER_ID = 'customer_ref';
    const A_DATE = 'date';
    const A_DEPTH = 'depth';
    const A_PARENT_ID = 'parent_ref';
    const A_PATH = 'path';
    const ENTITY_NAME = 'prxgt_dwnl_snap';

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::get(self::A_CUSTOMER_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        $result = parent::get(self::A_DATE);
        return $result;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        $result = parent::get(self::A_DEPTH);
        return $result;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        $result = parent::get(self::A_PARENT_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $result = parent::get(self::A_PATH);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_DATE, self::A_CUSTOMER_ID];
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::set(self::A_CUSTOMER_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setDate($data)
    {
        parent::set(self::A_DATE, $data);
    }

    /**
     * @param int $data
     */
    public function setDepth($data)
    {
        parent::set(self::A_DEPTH, $data);
    }

    /**
     * @param int $data
     */
    public function setParentId($data)
    {
        parent::set(self::A_PARENT_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setPath($data)
    {
        parent::set(self::A_PATH, $data);
    }
}