<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Customer\Model;


class AccountManagement
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCust;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust
    ) {
        $this->repoCust = $repoCust;
        $this->repoDwnlCust = $repoDwnlCust;
    }

    /**
     * Look up for customer's email by MLM ID
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
}