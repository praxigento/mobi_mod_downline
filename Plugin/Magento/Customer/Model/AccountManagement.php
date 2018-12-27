<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Magento\Customer\Model;

class AccountManagement
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $daoCust;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Helper\Registry */
    private $hlpReg;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $daoCust,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Helper\Registry $hlpReg
    ) {
        $this->daoCust = $daoCust;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->hlpReg = $hlpReg;
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
            $found = $this->daoDwnlCust->getByMlmId($mlmId);
            if ($found) {
                $custId = $found->getCustomerRef();
                $customer = $this->daoCust->getById($custId);
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
     * Extract country code and save into Magento registry when customer is created through adminhtml.
     *
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param null $password
     * @param string $redirectUrl
     * @return array
     */
    public function beforeCreateAccount(
        \Magento\Customer\Model\AccountManagement $subject,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ) {
        $addrs = $customer->getAddresses();
        if (is_array($addrs)) {
            foreach ($addrs as $addr) {
                $countryCode = $addr->getCountryId();
                $this->hlpReg->putCustomerCountry($countryCode);
                $isBilling = $addr->isDefaultBilling();
                if ($isBilling) break; // get any address but exit the loop if default billing
            }
        }
        return [$customer, $password, $redirectUrl];
    }
}