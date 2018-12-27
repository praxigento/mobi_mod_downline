<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Data;

class Snap
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CUSTOMER_REF = 'customer_ref';
    const A_DATE = 'date';
    const A_DEPTH = 'depth';
    const A_PARENT_REF = 'parent_ref';
    const A_PATH = 'path';
    const ENTITY_NAME = 'prxgt_dwnl_snap';

    /**
     * @return int
     */
    public function getCustomerRef()
    {
        $result = parent::get(self::A_CUSTOMER_REF);
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
    public function getParentRef()
    {
        $result = parent::get(self::A_PARENT_REF);
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
        return [self::A_DATE, self::A_CUSTOMER_REF];
    }

    /**
     * @param int $data
     */
    public function setCustomerRef($data)
    {
        parent::set(self::A_CUSTOMER_REF, $data);
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
    public function setParentRef($data)
    {
        parent::set(self::A_PARENT_REF, $data);
    }

    /**
     * @param string $data
     */
    public function setPath($data)
    {
        parent::set(self::A_PATH, $data);
    }
}