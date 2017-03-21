<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Transaction\Get;

/**
 * Request to get transactions entries for customer.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\Api\Response
{
    /**
     * @return \Praxigento\Downline\Api\Transaction\Get\Response\Entry[]
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Transaction\Get\Response\Entry[] $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }

}