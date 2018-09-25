<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Helper;

/**
 * Access Magento registry store.
 */
class Registry
{
    const CUST_COUNTRY = 'prxgtCustCountry';
    const CUST_MLM_ID = 'prxgtCustMlmId';
    const PARENT_MAGE_ID = 'prxgtParentMageId';

    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function getCustomerCountry()
    {
        $result = $this->registry->registry(self::CUST_COUNTRY);
        return $result;
    }

    public function getCustomerMlmId()
    {
        $result = $this->registry->registry(self::CUST_MLM_ID);
        return $result;
    }

    public function getParentId()
    {
        $result = $this->registry->registry(self::PARENT_MAGE_ID);
        return $result;
    }

    public function putCustomerCountry($data)
    {
        if ($this->registry->registry(self::CUST_COUNTRY)) {
            $this->registry->unregister(self::CUST_COUNTRY);
        }
        $this->registry->register(self::CUST_COUNTRY, $data);
    }

    public function putCustomerMlmId($data)
    {
        if ($this->registry->registry(self::CUST_MLM_ID)) {
            $this->registry->unregister(self::CUST_MLM_ID);
        }
        $this->registry->register(self::CUST_MLM_ID, $data);
    }

    public function putParentId($data)
    {
        if ($this->registry->registry(self::PARENT_MAGE_ID)) {
            $this->registry->unregister(self::PARENT_MAGE_ID);
        }
        $this->registry->register(self::PARENT_MAGE_ID, $data);
    }
}