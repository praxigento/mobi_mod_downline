<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Data;

class Customer
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_COUNTRY_CODE = 'country_code';
    const A_CUSTOMER_REF = 'customer_ref';
    const A_DEPTH = 'depth';
    const A_MLM_ID = 'mlm_id';
    const A_PARENT_REF = 'parent_ref';
    const A_PATH = 'path';
    const A_REFERRAL_CODE = 'referral_code';
    const ENTITY_NAME = 'prxgt_dwnl_customer';


    /**
     * @return string
     */
    public function getCountryCode()
    {
        $result = parent::get(self::A_COUNTRY_CODE);
        return $result;
    }

    /**
     * @return int
     */
    public function getCustomerRef()
    {
        $result = parent::get(self::A_CUSTOMER_REF);
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
     * @return string
     */
    public function getMlmId()
    {
        $result = parent::get(self::A_MLM_ID);
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
        return [self::A_CUSTOMER_REF];
    }

    /**
     * @return string
     */
    public function getReferralCode()
    {
        $result = parent::get(self::A_REFERRAL_CODE);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setCountryCode($data)
    {
        parent::set(self::A_COUNTRY_CODE, $data);
    }

    /**
     * @param int $data
     */
    public function setCustomerRef($data)
    {
        parent::set(self::A_CUSTOMER_REF, $data);
    }

    /**
     * @param int $data
     */
    public function setDepth($data)
    {
        parent::set(self::A_DEPTH, $data);
    }

    /**
     * @param string $data
     */
    public function setMlmId($data)
    {
        parent::set(self::A_MLM_ID, $data);
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

    /**
     * @param string $data
     */
    public function setReferralCode($data)
    {
        parent::set(self::A_REFERRAL_CODE, $data);
    }

}