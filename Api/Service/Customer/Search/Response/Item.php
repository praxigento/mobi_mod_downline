<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Search\Response;

/**
 * Modules's shell extends underline core service data object directly.
 *
 * (Define getters explicitly to use with Swagger tool)
 */
class Item
    extends \Praxigento\Core\Api\Service\Customer\Search\Response\Item
{
    const COUNTRY = 'country';
    const MLM_ID = 'mlm_id';
    const PATH_FULL = 'path_full';

    /**
     * @return string|null
     */
    public function getCountry()
    {
        $result = parent::get(self::COUNTRY);
        return $result;
    }

    /**
     * @return string|null
     */
    public function getMlmId()
    {
        $result = parent::get(self::MLM_ID);
        return $result;
    }

    /**
     * Full path to the customer (including own ID) in downline.
     *
     * @return string|null
     */
    public function getPathFull()
    {
        $result = parent::get(self::PATH_FULL);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setCountry($data)
    {
        parent::set(self::COUNTRY, $data);
    }

    /**
     * @param string $data
     */
    public function setMlmId($data)
    {
        parent::set(self::MLM_ID, $data);
    }

    /**
     * Full path to the customer (including own ID) in downline.
     *
     * @param string $data
     */
    public function setPathFull($data)
    {
        parent::set(self::PATH_FULL, $data);
    }
}