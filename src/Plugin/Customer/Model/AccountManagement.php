<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Customer\Model;

use Praxigento\Downline\Observer\CustomerSaveAfterDataObject as Observer;

class AccountManagement
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCust;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust
    ) {
        $this->registry = $registry;
        $this->repoCust = $repoCust;
        $this->repoDwnlCust = $repoDwnlCust;
    }

    /**
     * Look up for customer's email by MLM ID on authentication.
     *
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param $username
     * @param $password
     * @return array
     */
    public function beforeAuthenticate(
        \Magento\Customer\Model\AccountManagement $subject,
        $username,
        $password
    ) {
        try {
            $mlmId = trim($username);
            $found = $this->repoDwnlCust->getByMlmId($mlmId);
            if ($found) {
                $custId = $found->getCustomerId();
                $customer = $this->repoCust->getById($custId);
                if ($customer instanceof \Magento\Customer\Api\Data\CustomerInterface) {
                    $username = $customer->getEmail();
                }
            }
        } catch (\Throwable $e) {
            /* stealth exceptions */
        }
        return [$username, $password];
    }

    /**
     * Save customer country code into the registry to be processed in downline when new customer is created.
     *
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param $hash
     * @param string $redirectUrl
     * @return array
     */
    public function beforeCreateAccountWithPasswordHash(
        \Magento\Customer\Model\AccountManagement $subject,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $hash,
        $redirectUrl = ''
    ) {
        if ($customer) {
            /** @var \Magento\Customer\Api\Data\AddressInterface[] $addrs */
            $addrs = $customer->getAddresses();
            if (is_array($addrs)) {
                foreach ($addrs as $addr) {
                    if ($addr->getCountryId()) {
                        $countryId = $addr->getCountryId();
                    }
                    if ($addr->isDefaultBilling()) break;
                }
                if ($this->registry->registry(Observer::A_CUST_COUNTRY)) $this->registry->unregister(Observer::A_CUST_COUNTRY);
                $this->registry->register(Observer::A_CUST_COUNTRY, $countryId);
            }
        }
        return [$customer, $hash, $redirectUrl];
    }
}