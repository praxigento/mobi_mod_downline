<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

/**
 * Response to get downline subtree.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Response
    extends \Praxigento\Core\App\Api\Web\Response
{
    /**
     * @return \Praxigento\Downline\Api\Tree\Get\Response\Data|null
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @return \Praxigento\Downline\Api\Tree\Get\Request|null
     */
    public function getRequest()
    {
        $result = parent::getRequest();
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }

    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Request $data
     */
    public function setRequest($data)
    {
        parent::setRequest($data);
    }
}