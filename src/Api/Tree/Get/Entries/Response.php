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
    extends \Praxigento\Core\App\Web\Response
{
    /**
     * @return \Praxigento\Downline\Api\Tree\Get\Entries\Response\Data|null
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @return \Praxigento\Downline\Api\Tree\Get\Entries\Request|null
     */
    public function getRequest()
    {
        $result = parent::getRequest();
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Entries\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }

    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Entries\Request $data
     */
    public function setRequest($data)
    {
        parent::setRequest($data);
    }
}