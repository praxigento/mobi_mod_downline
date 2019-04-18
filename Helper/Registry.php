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
    private const CUST_COUNTRY = 'prxgtDwnlCustCountry';
    private const QUOTE_ID = 'prxgtDwnlQuoteId';

    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @return string 2 chars country code
     */
    public function getCustomerCountry()
    {
        $result = $this->registry->registry(self::CUST_COUNTRY);
        return $result;
    }

    /**
     * Quote ID for newly created orders.
     *
     * @return int
     */
    public function getQuoteId()
    {
        $result = (int)$this->registry->registry(self::QUOTE_ID);
        return $result;
    }

    /**
     * @param string $data 2 chars country code
     */
    public function putCustomerCountry($data)
    {
        if ($this->registry->registry(self::CUST_COUNTRY)) {
            $this->registry->unregister(self::CUST_COUNTRY);
        }
        $this->registry->register(self::CUST_COUNTRY, $data);
    }

    /**
     * Quote ID for newly created orders.
     *
     * @param int $data
     */
    public function putQuoteId($data)
    {
        if ($this->registry->registry(self::QUOTE_ID)) {
            $this->registry->unregister(self::QUOTE_ID);
        }
        $this->registry->register(self::QUOTE_ID, $data);
    }
}