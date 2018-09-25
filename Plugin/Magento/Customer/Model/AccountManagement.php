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

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $daoCust,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust
    ) {
        $this->daoCust = $daoCust;
        $this->daoDwnlCust = $daoDwnlCust;
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
                $custId = $found->getCustomerId();
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
}