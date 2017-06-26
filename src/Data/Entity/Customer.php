<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Data\Entity;

class Customer
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_COUNTRY_CODE = 'country_code';
    const ATTR_CUSTOMER_ID = 'customer_ref';
    const ATTR_DEPTH = 'depth';
    /* TODO: rename to MLM ID */
    const ATTR_HUMAN_REF = 'mlm_id';
    const ATTR_PARENT_ID = 'parent_ref';
    const ATTR_PATH = 'path';
    const ATTR_REFERRAL_CODE = 'referral_code';
    const ENTITY_NAME = 'prxgt_dwnl_customer';


    /**
     * @return string
     */
    public function getCountryCode()
    {
        $result = parent::get(self::ATTR_COUNTRY_CODE);
        return $result;
    }

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
    public function getDepth()
    {
        $result = parent::get(self::ATTR_DEPTH);
        return $result;
    }

    /**
     * @return string
     */
    public function getHumanRef()
    {
        $result = parent::get(self::ATTR_HUMAN_REF);
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

    /**
     * @return string
     */
    public function getPath()
    {
        $result = parent::get(self::ATTR_PATH);
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
        $result = parent::get(self::ATTR_REFERRAL_CODE);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setCountryCode($data)
    {
        parent::set(self::ATTR_COUNTRY_CODE, $data);
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::set(self::ATTR_CUSTOMER_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setDepth($data)
    {
        parent::set(self::ATTR_DEPTH, $data);
    }

    /**
     * @param string $data
     */
    public function setHumanRef($data)
    {
        parent::set(self::ATTR_HUMAN_REF, $data);
    }

    /**
     * @param int $data
     */
    public function setParentId($data)
    {
        parent::set(self::ATTR_PARENT_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setPath($data)
    {
        parent::set(self::ATTR_PATH, $data);
    }

    /**
     * @param string $data
     */
    public function setReferralCode($data)
    {
        parent::set(self::ATTR_REFERRAL_CODE, $data);
    }

}