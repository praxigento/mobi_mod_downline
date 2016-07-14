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
    const ATTR_REFERRAL_CODE = 'referral_code';
    const ENTITY_NAME = 'prxgt_dwnl_customer';


    /**
     * @return string
     */
    public function getCountryCode()
    {
        $result = parent::getData(self::ATTR_COUNTRY_CODE);
        return $result;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::getData(self::ATTR_CUSTOMER_ID);
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
     * @return string
     */
    public function getHumanRef()
    {
        $result = parent::getData(self::ATTR_HUMAN_REF);
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
        return [self::ATTR_CUSTOMER_ID];
    }

    /**
     * @return string
     */
    public function getReferralCode()
    {
        $result = parent::getData(self::ATTR_REFERRAL_CODE);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setCountryCode($data)
    {
        parent::setData(self::ATTR_COUNTRY_CODE, $data);
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::setData(self::ATTR_CUSTOMER_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setDepth($data)
    {
        parent::setData(self::ATTR_DEPTH, $data);
    }

    /**
     * @param string $data
     */
    public function setHumanRef($data)
    {
        parent::setData(self::ATTR_HUMAN_REF, $data);
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

    /**
     * @param string $data
     */
    public function setReferralCode($data)
    {
        parent::setData(self::ATTR_REFERRAL_CODE, $data);
    }

}