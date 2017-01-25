<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get\Entries;

/**
 * Request to get entries for downline tree node.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\Api\Response
{
    /**
     * @return \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response\Data|null
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }
}