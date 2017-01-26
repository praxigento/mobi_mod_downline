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
 * @method void setMaxDepth(int $data)
 * @method void setMaxEntries(int $data)
 * @method void setPeriod(string $data)
 * @method void setRequestReturn(bool $data)
 * @method void setRootNode(int $data)
 *
 */
class Request
    extends \Flancer32\Lib\Data
{

    /**
     * Max depth for entries starting from request's root node.
     *
     * @return int|null
     */
    public function getMaxDepth()
    {
        $result = parent::getMaxDepth();
        return $result;
    }

    /**
     * Max number of entries in result set.
     *
     * @return int|null
     */
    public function getMaxEntries()
    {
        $result = parent::getMaxEntries();
        return $result;
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