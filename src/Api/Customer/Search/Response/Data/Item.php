<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Customer\Search\Response\Data;

class Item
    extends \Praxigento\Core\Data
{
    const EMAIL = 'email';
    const ID = 'id';
    const MLM_ID = 'mlm_id';
    const NAME_FIRST = 'name_first';
    const NAME_LAST = 'name_last';

    /**
     * @return string
     */
    public function getEmail()
    {
        $result = parent::get(self::EMAIL);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::get(self::ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getMlmId()
    {
        $result = parent::get(self::MLM_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getNameFirst()
    {
        $result = parent::get(self::NAME_FIRST);
        return $result;
    }

    /**
     * @return string
     */
    public function getNameLast()
    {
        $result = parent::get(self::NAME_LAST);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setEmail($data)
    {
        parent::set(self::EMAIL, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::set(self::ID, $data);
    }

    /**
     * @param string $data
     */
    public function setMlmId($data)
    {
        parent::set(self::MLM_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setNameFirst($data)
    {
        parent::set(self::NAME_FIRST, $data);
    }

    /**
     * @param string $data
     */
    public function setNameLast($data)
    {
        parent::set(self::NAME_LAST, $data);
    }
}