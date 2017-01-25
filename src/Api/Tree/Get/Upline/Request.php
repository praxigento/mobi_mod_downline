<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get\Upline;

/**
 * Request to get entries for downline tree node.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 * @method void setPeriod(string $data)
 * @method void setTargetNode(int $data)
 *
 */
class Request
    extends \Flancer32\Lib\Data
{
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
     * Customer ID for the target node.
     *
     * @return int|null
     */
    public function getTargetNode()
    {
        $result = parent::getTargetNode();
        return $result;
    }

}