<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Magento\Quote\Model\QuoteRepository;


class SaveHandler
{
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Customer\Model\Session $session
    ) {
        $this->session = $session;
    }

    /**
     * Force "saveInAddressBook" attribute for billing address to get country code for downline on guest checkout.
     *
     * @param \Magento\Quote\Model\QuoteRepository\SaveHandler $subject
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return array
     */
    public function beforeSave(
        \Magento\Quote\Model\QuoteRepository\SaveHandler $subject,
        \Magento\Quote\Api\Data\CartInterface $quote
    ) {
        $isLoggedIn = $this->session->isLoggedIn();
        if (!$isLoggedIn) {
            $address = $quote->getBillingAddress();
            if ($address instanceof \Magento\Quote\Model\Quote\Address) {
                $address->setSaveInAddressBook(true);
//                $quote->setBillingAddress($address);
            }
        }
        return [$quote];
    }
}