<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Customer\Search;

/**
 * Response to get suggestions for customers by key (name/email/mlm_id).
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\Api\Response
{
    /**
     * @return \Praxigento\Downline\Api\Customer\Search\Response\Data
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        $result = parent::getRequest();
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Customer\Search\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }

    /**
     *
     * @param string $data
     */
    public function setRequest($data)
    {
        parent::setRequest($data);
    }

}