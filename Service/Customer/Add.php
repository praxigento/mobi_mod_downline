<?php
/** @noinspection PhpDocMissingThrowsInspection */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\Add\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Add\Response as AResponse;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Change as EDwnlChange;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

/**
 * Add customer to downline and new entry to change log.
 */
class Add
    implements \Praxigento\Downline\Api\Service\Customer\Add
{
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    private $daoDwnlChange;
    /** @var  \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Helper\Config */
    private $hlpConfig;
    /** @var  \Praxigento\Downline\Api\Helper\Referral */
    private $hlpReferral;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Magento\Framework\App\State */
    private $state;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Repo\Dao\Change $daoDwnlChange,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Api\Helper\Referral $hlpReferral,
        \Praxigento\Downline\Helper\Config $hlpConfig
    ) {
        $this->state = $state;
        $this->logger = $logger;
        $this->daoDwnlChange = $daoDwnlChange;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->hlpReferral = $hlpReferral;
        $this->hlpConfig = $hlpConfig;
    }

    /**
     * @param ARequest $req
     * @return AResponse
     * @throws \Exception
     */
    public function exec($req)
    {
        assert($req instanceof ARequest);

        /** define local working data */
        $customerId = $req->getCustomerId();
        $parentId = $req->getParentId();
        $mlmId = $req->getMlmId();
        $refCode = $req->getReferralCode();
        $countryCode = $req->getCountryCode();
        $date = $req->getDate();
        $this->logger->info("Add new customer #$customerId with parent #$parentId into downline tree.");

        /** perform processing */
        /* define referred parent if no parent in request */
        if (!$parentId) {
            $parentId = $this->getReferredParentId($customerId);
        }
        if ($customerId == $parentId) {
            /* add root node */
            $this->logger->info("This is root node (customer ID is equal to parent ID).");
            $path = Cfg::DTPS;
            $depth = Cfg::INIT_DEPTH;
        } else {
            /* get parent data by parent Mage id */
            $parent = $this->daoDwnlCust->getById($parentId);
            $parentPath = $parent->getPath();
            $parentDepth = $parent->getDepth();
            $path = $parentPath . $parentId . Cfg::DTPS;
            $depth = $parentDepth + 1;
        }

        /* add customer to downline */
        $customer = new EDwnlCust();
        $customer->setCountryCode($countryCode);
        $customer->setCustomerRef($customerId);
        $customer->setDepth($depth);
        $customer->setMlmId($mlmId);
        $customer->setParentRef($parentId);
        $customer->setPath($path);
        $customer->setReferralCode($refCode);
        $this->daoDwnlCust->create($customer);

        /* save log record to the changes registry */
        $log = new EDwnlChange();
        $log->setCustomerRef($customerId);
        $log->setParentRef($parentId);
        $log->setDateChanged($date);
        $logId = $this->daoDwnlChange->create($log);
        $this->logger->debug("Downline changes are logged in registry with date: $date.");
        $this->logger->debug("New change log record #$logId is inserted (customer: $customerId, parent: $parentId, date: $date).");
        $this->logger->info("New customer #$customerId with parent #$parentId is added to downline tree.");

        /** compose result */
        $result = new AResponse();
        $result->markSucceed();
        return $result;
    }


    /**
     * Analyze referral code and get parent for the customer if parent ID was missed in request.
     *
     * @param int $customerId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getReferredParentId($customerId)
    {
        /* use customer ID as parent ID if parent ID cannot be defined */
        $result = $customerId;
        $area = $this->state->getAreaCode();
        if ($area == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            /* use default MLM ID from config */
            $defRootMlmId = $this->hlpConfig->getReferralsRootAnonymous();
            $parent = $this->daoDwnlCust->getByMlmId($defRootMlmId);
            if ($parent) {
                $result = $parent->getCustomerRef();
                $this->logger->info("Default root parent #$result is used for customer #$customerId (admin mode).");
            }
        } else {
            /* try to extract referral code from Mage registry */
            $code = $this->hlpReferral->getReferralCode();
            if ($code) {
                /* this is a referral customer, use parent from referral code */
                $parent = $this->daoDwnlCust->getByReferralCode($code);
                if ($parent) {
                    $result = $parent->getCustomerRef();
                    $this->logger->info("Referral parent #$result is used for customer #$customerId.");
                }
            } else {
                /* this is anonymous customer, use parent from config */
                $anonRootMlmId = $this->hlpConfig->getReferralsRootAnonymous();
                $parent = $this->daoDwnlCust->getByMlmId($anonRootMlmId);
                if ($parent) {
                    $result = $parent->getCustomerRef();
                    $this->logger->info("Anonymous root parent #$result is used for customer #$customerId.");
                }
            }
        }
        return $result;
    }
}