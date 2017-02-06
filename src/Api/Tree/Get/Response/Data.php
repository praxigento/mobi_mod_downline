<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get\Response;

/**
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Flancer32\Lib\Data
{
    /**
     * @return \Praxigento\Downline\Api\Data\Tree\Node[]|null
     */
    public function getNodes()
    {
        $result = parent::getNodes();
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Data\Tree\Node[] $data
     */
    public function setNodes($data)
    {
        parent::setNodes($data);
    }
}