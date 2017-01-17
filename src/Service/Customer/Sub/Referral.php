<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Sub;


class Referral
{
    /** @var \Praxigento\Downline\Helper\Config */
    protected $hlpConfig;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var  \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $repoCustomer;
    /** @var  \Praxigento\Downline\Tool\IReferral */
    protected $toolReferral;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoCustomer,
        \Praxigento\Downline\Tool\IReferral $toolReferral,
        \Praxigento\Downline\Helper\Config $hlpConfi
    ) {
        $this->logger = $logger;
        $this->repoCustomer = $repoCustomer;
        $this->toolReferral = $toolReferral;
        $this->hlpConfig = $hlpConfi;
    }

    /**
     * Wrapper for Referral tool's method.
     *
     * @return string
     */
    public function getDefaultCountryCode()
    {
        $result = $this->toolReferral->getDefaultCountryCode();
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
        $code = $this->toolReferral->getReferralCode();
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