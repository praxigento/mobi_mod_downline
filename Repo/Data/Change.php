<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Data;

class Change
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CUSTOMER_ID = 'customer_ref';
    const A_DATE_CHANGED = 'date_changed';
    const A_ID = 'id';
    const A_PARENT_ID = 'parent_ref';
    const ENTITY_NAME = 'prxgt_dwnl_change';

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::get(self::A_CUSTOMER_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getDateChanged()
    {
        $result = parent::get(self::A_DATE_CHANGED);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::get(self::A_ID);
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

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
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
    public function setDateChanged($data)
    {
        parent::set(self::A_DATE_CHANGED, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::set(self::A_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setParentId($data)
    {
        parent::set(self::A_PARENT_ID, $data);
    }
}