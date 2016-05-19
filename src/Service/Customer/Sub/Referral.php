<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Sub;


class Referral
{
    /** @var  \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $_repoCustomer;
    /** @var  \Praxigento\Downline\Tool\IReferral */
    protected $_toolReferralCode;

    public function __construct(
        \Praxigento\Downline\Repo\Entity\ICustomer $repoCustomer,
        \Praxigento\Downline\Tool\IReferral $toolReferralCode
    ) {
        $this->_repoCustomer = $repoCustomer;
        $this->_toolReferralCode = $toolReferralCode;
    }

    /**
     * Wrapper for Referral tool's method.
     *
     * @return string
     */
    public function getDefaultCountryCode()
    {
        $result = $this->_toolReferralCode->getDefaultCountryCode();
        return $result;
    }

    /**
     * Analyze referral code and get parent for the customer.
     *
     * @param int $customerId
     * @param int $parentId
     * @return int
     */
    public function getReferredParentId($customerId, $parentId)
    {
        /* use customer ID as parent ID if parent ID is missed */
        $result = ($parentId) ? $parentId : $customerId;
        $code = $this->_toolReferralCode->getReferralCode();
        if ($code) {
            $parentDo = $this->_repoCustomer->getByReferralCode($code);
            if ($parentDo) {
                $result = $parentDo->getCustomerId();
            }
        }
        return $result;
    }
}