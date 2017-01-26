<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Data\Tree;

/**
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 * @method void setCountryCode(string $data)
 * @method void setCustomerEmail(string $data)
 * @method void setCustomerId(int $data)
 * @method void setCustomerMlmId(string $data)
 * @method void setCustomerName(string $data)
 * @method void setDepthInTree(int $data)
 * @method void setParentId(int $data)
 * @method void setPath(string $data)
 *
 */
class Entry
    extends \Flancer32\Lib\Data
{
    /**
     * @return string
     */
    public function getCountryCode()
    {
        $result = parent::getCountryCode();
        return $result;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        $result = parent::getCustomerEmail();
        return $result;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        $result = parent::getCustomerId();
        return $result;
    }

    /**
     * @return string
     */
    public function getCustomerMlmId()
    {
        $result = parent::getCustomerMlmId();
        return $result;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $result = parent::getCustomerName();
        return $result;
    }

    /**
     * @return string
     */
    public function getDepthInTree()
    {
        $result = parent::getDepthInTree();
        return $result;
    }

    /**
     * @return string
     */
    public function getParentId()
    {
        $result = parent::getParentId();
        return $result;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $result = parent::getPath();
        return $result;
    }

}