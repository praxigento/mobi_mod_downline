<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Magento\Quote\Model\Quote;

/**
 * Force "saveInAddressBook" attribute for billing address to get country code for downline on guest checkout.
 */
class Address
{
    public function afterLoad(
        \Magento\Quote\Model\Quote\Address $subject,
        \Magento\Quote\Model\Quote\Address $result
    ) {
        if ($result instanceof \Magento\Quote\Model\Quote\Address) {
            $type = $result->getAddressType();
            if ($type == \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_BILLING) {
                $customerId = $result->getCustomerId();
                if (is_null($customerId)) {
                    /* customer is guest (?) */
                    $result->setSaveInAddressBook(true);
                }
            }
        }
        return $result;
    }
}