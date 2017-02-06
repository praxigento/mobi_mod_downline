<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

/**
 * Request to get downline subtree.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Flancer32\Lib\Data
{

    /**
     * Max depth for nodes layers starting from request's root node.
     *
     * @return int|null
     */
    public function getMaxDepth()
    {
        $result = parent::getMaxDepth();
        return $result;
    }

    /**
     * Max depth for nodes layers starting from request's root node.
     *
     * @param int $data
     */
    public function setMaxDepth($data)
    {
        parent::setMaxDepth($data);
    }

    /**
     * Period to get snapped data ('YYYYMM', 'YYYYMMDD'), if missed - actual data will be returned.
     *
     * @param string $data
     */
    public function setPeriod($data)
    {
        parent::setPeriod($data);
    }

    /**
     * Flag to include request data in response.
     *
     * @param bool $data
     */
    public function setRequestReturn($data)
    {
        parent::setRequestReturn($data);
    }

    /**
     * Customer ID for the root node. Current customer ID is used on frontend if missed. All nodes will be returned
     * in adminhtml if missed.
     *
     * @param int $data
     */
    public function setRootNode($data)
    {
        parent::setRootNode($data);
    }

    /**
     * Period to get snapped data ('YYYYMM', 'YYYYMMDD'), if missed - actual data will be returned.
     *
     * @return string|null
     */
    public function getPeriod()
    {
        $result = parent::getPeriod();
        return $result;
    }

    /**
     * Flag to include request data in response.
     *
     * @return bool|null
     */
    public function getRequestReturn()
    {
        $result = parent::getRequestReturn();
        return $result;
    }

    /**
     * Customer ID for the root node. Current customer ID is used on frontend if missed. All nodes will be returned
     * in adminhtml if missed.
     *
     * @return int|null
     */
    public function getRootNode()
    {
        $result = parent::getRootNode();
        return $result;
    }


}