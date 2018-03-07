<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Sub;


class Referral
{
    /** @var \Praxigento\Downline\Helper\Config */
    protected $hlpConfig;
    /** @var  \Praxigento\Downline\Api\Helper\Referral */
    protected $hlpReferral;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var  \Praxigento\Downline\Repo\Entity\Customer */
    protected $repoCustomer;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Downline\Repo\Entity\Customer $repoCustomer,
        \Praxigento\Downline\Api\Helper\Referral $hlpReferral,
        \Praxigento\Downline\Helper\Config $hlpConfi
    ) {
        $this->logger = $logger;
        $this->repoCustomer = $repoCustomer;
        $this->hlpReferral = $hlpReferral;
        $this->hlpConfig = $hlpConfi;
    }

    /**
     * Wrapper for Referral tool's method.
     *
     * @return string
     */
    public function getDefaultCountryCode()
    {
        $result = $this->hlpReferral->getDefaultCountryCode();
        return $result;
    }

    /**
     * Analyze referral code and get parent for the customer.
     *
     * @param int $customerId
     * @return int
     */
    public function getReferredParentId($customerId)
    {
        /* use customer ID as parent ID if parent ID cannot be defined */
        $result = $customerId;
        /* extract referral code from Mage registry */
        $code = $this->hlpReferral->getReferralCode();
        if ($code) {
            /* this is a referral customer, use parent from referral code */
            $parentDo = $this->repoCustomer->getByReferralCode($code);
            if ($parentDo) {
                $result = $parentDo->getCustomerId();
                $this->logger->info("Referral parent #$result is used for customer #$customerId.");
            }
        } else {
            /* this is anonymous customer, use parent from config */
            $anonRootId = $this->hlpConfig->getReferralsRootAnonymous();
            $parentDo = $this->repoCustomer->getById($anonRootId);
            if ($parentDo) {
                $result = $parentDo->getCustomerId();
                $this->logger->info("Anonymous root parent #$result is used for customer #$customerId.");
            }
        }
        return $result;
    }
}