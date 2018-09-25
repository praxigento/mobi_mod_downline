<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Magento\Sales\Model\Order;


class OrderCustomerExtractor
{
    /** @var \Praxigento\Downline\Helper\Registry */
    private $hlpRegistry;

    public function __construct(
        \Praxigento\Downline\Helper\Registry $hlpRegistry
    ) {
        $this->hlpRegistry = $hlpRegistry;
    }

    public function afterExtract(
        \Magento\Sales\Model\Order\OrderCustomerExtractor $subject,
        \Magento\Customer\Api\Data\CustomerInterface $result
    ) {
        $addresses = $result->getAddresses();
        if (
            is_array($addresses) &&
            count($addresses)
        ) {
            $countryId = null;
            foreach ($addresses as $address) {
                if ($address->getCountryId()) {
                    $countryId = $address->getCountryId();
                }
                if ($address->isDefaultBilling()) break;
            }
            if ($countryId) {
                $this->hlpRegistry->putCustomerCountry($countryId);
            }
        }
        return $result;
    }
}