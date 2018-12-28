<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Repo\Data\Change;

class Group
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CUSTOMER_REF = 'customer_ref';
    const A_DATE_CHANGED = 'date_changed';
    const A_GROUP_NEW = 'group_new';
    const A_GROUP_OLD = 'group_old';
    const A_ID = 'id';
    const ENTITY_NAME = 'prxgt_dwnl_change_group';

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
    public function getDateChanged()
    {
        $result = parent::get(self::A_DATE_CHANGED);
        return $result;
    }

    /**
     * @return int
     */
    public function getGroupNew()
    {
        $result = parent::get(self::A_GROUP_NEW);
        return $result;
    }

    /**
     * @return int
     */
    public function getGroupOld()
    {
        $result = parent::get(self::A_GROUP_OLD);
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
    public function getParentRef()
    {
        $result = parent::get(self::A_PARENT_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
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
    public function setDateChanged($data)
    {
        parent::set(self::A_DATE_CHANGED, $data);
    }

    /**
     * @param int $data
     */
    public function setGroupNew($data)
    {
        parent::set(self::A_GROUP_NEW, $data);
    }

    /**
     * @param int $data
     */
    public function setGroupOld($data)
    {
        parent::set(self::A_GROUP_OLD, $data);
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
    public function setParentRef($data)
    {
        parent::set(self::A_PARENT_REF, $data);
    }
}