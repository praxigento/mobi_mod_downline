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
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    protected $logger;
    /** @var  \Praxigento\Downline\Repo\Dao\Customer */
    protected $daoCustomer;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Repo\Dao\Customer $daoCustomer,
        \Praxigento\Downline\Api\Helper\Referral $hlpReferral,
        \Praxigento\Downline\Helper\Config $hlpConfi
    ) {
        $this->logger = $logger;
        $this->daoCustomer = $daoCustomer;
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
            $parentDo = $this->daoCustomer->getByReferralCode($code);
            if ($parentDo) {
                $result = $parentDo->getCustomerId();
                $this->logger->info("Referral parent #$result is used for customer #$customerId.");
            }
        } else {
            /* this is anonymous customer, use parent from config */
            $anonRootMlmId = $this->hlpConfig->getReferralsRootAnonymous();
            $parentDo = $this->daoCustomer->getByMlmId($anonRootMlmId);
            if ($parentDo) {
                $result = $parentDo->getCustomerId();
                $this->logger->info("Anonymous root parent #$result is used for customer #$customerId.");
            }
        }
        return $result;
    }
}