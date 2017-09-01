<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Data\Agg;

/**
 * Aggregate for customer and downline data.
 *
 * This is new preferred place for repository data objects (instead of ...\Data\Agg).
 *
 */
class Downline
    extends \Praxigento\Core\Data
{
    const A_COUNTRY = 'country';
    const A_CUSTOMER_REF = 'customer_ref';
    const A_DEPTH = 'depth';
    const A_EMAIL = 'email';
    const A_MLM_ID = 'mlm_id';
    const A_NAME_FIRST = 'name_first';
    const A_NAME_LAST = 'name_last';
    const A_NAME_MIDDLE = 'name_middle';
    const A_PARENT_REF = 'parent_ref';
    const A_PATH = 'path';

    public function getCountry()
    {
        $result = parent::get(self::A_COUNTRY);
        return $result;
    }

    public function getCustomerRef()
    {
        $result = parent::get(self::A_CUSTOMER_REF);
        return $result;
    }

    public function getDepth()
    {
        $result = parent::get(self::A_DEPTH);
        return $result;
    }

    public function getEmail()
    {
        $result = parent::get(self::A_EMAIL);
        return $result;
    }

    public function getMlmId()
    {
        $result = parent::get(self::A_MLM_ID);
        return $result;
    }

    public function getNameFirst()
    {
        $result = parent::get(self::A_NAME_FIRST);
        return $result;
    }

    public function getNameLast()
    {
        $result = parent::get(self::A_NAME_LAST);
        return $result;
    }

    public function getNameMiddle()
    {
        $result = parent::get(self::A_NAME_MIDDLE);
        return $result;
    }

    public function getParentRef()
    {
        $result = parent::get(self::A_PARENT_REF);
        return $result;
    }

    public function getPath()
    {
        $result = parent::get(self::A_PATH);
        return $result;
    }

    public function setCountry($data)
    {
        parent::set(self::A_COUNTRY, $data);
    }

    public function setCustomerRef($data)
    {
        parent::set(self::A_CUSTOMER_REF, $data);
    }

    public function setDepth($data)
    {
        parent::set(self::A_DEPTH, $data);
    }

    public function setEmail($data)
    {
        parent::set(self::A_EMAIL, $data);
    }

    public function setMlmId($data)
    {
        parent::set(self::A_MLM_ID, $data);
    }

    public function setNameFirst($data)
    {
        parent::set(self::A_NAME_FIRST, $data);
    }

    public function setNameLast($data)
    {
        parent::set(self::A_NAME_LAST, $data);
    }

    public function setNameMiddle($data)
    {
        parent::set(self::A_NAME_MIDDLE, $data);
    }

    public function setParentRef($data)
    {
        parent::set(self::A_PARENT_REF, $data);
    }

    public function setPath($data)
    {
        parent::set(self::A_PATH, $data);
    }
}