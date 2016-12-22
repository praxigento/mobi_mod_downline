<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Service\ICustomer;

class Call implements ICustomer
{
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Downline\Repo\Entity\IChange */
    protected $_repoChange;
    /** @var  \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $_repoCustomer;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;
    /** @var  Sub\Referral */
    protected $_subReferral;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Downline\Repo\Entity\IChange $repoChange,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoCustomer,
        Sub\Referral $subReferral
    ) {
        $this->_logger = $logger;
        $this->_manTrans = $manTrans;
        $this->_repoGeneric = $repoGeneric;
        $this->_repoChange = $repoChange;
        $this->_repoCustomer = $repoCustomer;
        $this->_subReferral = $subReferral;
    }

    /**
     * Add new customer to downline and new entry to change log.
     *
     * @param Request\Add $request
     *
     * @return Response\Add
     */
    public function add(Request\Add $request)
    {
        $result = new Response\Add();
        $customerId = $request->getCustomerId();
        $parentId = $request->getParentId();
        $humanReference = $request->getReference();
        $countryCode = $request->getCountryCode();
        $date = $request->getDate();
        $this->_logger->info("Add new customer #$customerId with parent #$parentId to downline tree.");
        $def = $this->_manTrans->begin();
        try {
            /* define referred parent */
            $parentId = $this->_subReferral->getReferredParentId($customerId, $parentId);
            if ($customerId == $parentId) {
                /* add root node */
                $this->_logger->info("This is root node (customer id is equal to parent id).");
                $path = Cfg::DTPS;
                $depth = Cfg::INIT_DEPTH;
            } else {
                /* get parent data by parent Mage id */
                $data = $this->_repoCustomer->getById($parentId);
                $parentPath = $data->getPath();
                $parentDepth = $data->getDepth();
                $path = $parentPath . $parentId . Cfg::DTPS;
                $depth = $parentDepth + 1;
            }
            /* add customer to downline */
            $toAdd = [
                Customer::ATTR_CUSTOMER_ID => $customerId,
                Customer::ATTR_PARENT_ID => $parentId,
                Customer::ATTR_DEPTH => $depth,
                Customer::ATTR_PATH => $path,
                /* use own ID as referral code */
                Customer::ATTR_REFERRAL_CODE => $customerId
            ];
            if (isset($humanReference)) {
                $toAdd[Customer::ATTR_HUMAN_REF] = $humanReference;
            }
            if (isset($countryCode)) {
                $toAdd[Customer::ATTR_COUNTRY_CODE] = $countryCode;
            } else {
                $toAdd[Customer::ATTR_COUNTRY_CODE] = $this->_subReferral->getDefaultCountryCode();
            }
            $this->_repoCustomer->create($toAdd);
            /* save log record to changes registry */
            $formatted = $date;
            $toLog = [
                Change::ATTR_CUSTOMER_ID => $customerId,
                Change::ATTR_PARENT_ID => $parentId,
                Change::ATTR_DATE_CHANGED => $formatted
            ];
            $idLog = $this->_repoChange->create($toLog);
            if ($idLog) {
                $this->_logger->debug("Downline changes are logged in registry with date: $formatted.");
                $this->_logger->debug("New change log record #$idLog is inserted (customer: $customerId, parent: $parentId, date: $formatted).");
                $result->set($toAdd);
                $result->markSucceed();
                $this->_manTrans->commit($def);
                $this->_logger->info("New customer #$customerId with parent #$parentId is added to downline tree.");
            }
        } finally {
            $this->_manTrans->end($def);
        }

        return $result;
    }


    public function changeParent(Request\ChangeParent $request)
    {
        $result = new Response\ChangeParent();
        $customerId = $request->getCustomerId();
        $newParentId = $request->getNewParentId();
        $formatted = $request->getDate();
        $this->_logger->info("Set up new parent #$newParentId for customer #$customerId.");
        $def = $this->_manTrans->begin();
        try {
            /* get customer's downline  data */
            $data = $this->_repoCustomer->getById($customerId);
            $currParentId = $data->getParentId();;
            $currDepth = $data->getDepth();
            $currPath = $data->getPath();
            if ($currParentId == $newParentId) {
                /* nothing to change */
                $result->markSucceed();
                $this->_manTrans->commit($def);
                $this->_logger->notice("Current parent is the same as new one. Nothing to do.");
            } else {
                if ($customerId == $newParentId) {
                    /* change to root node */
                    $newCustomerDepth = Cfg::INIT_DEPTH;
                    $newCustomerPath = Cfg::DTPS;
                } else {
                    /* get new parent data */
                    $newParentData = $this->_repoCustomer->getById($newParentId);
                    $newParentDepth = $newParentData->getDepth();
                    $newParentPath = $newParentData->getPath();
                    $newCustomerDepth = $newParentDepth + 1;
                    $newCustomerPath = $newParentPath . $newParentId . Cfg::DTPS;
                }
                /* update customer with new data */
                $bind = [
                    Customer::ATTR_PARENT_ID => $newParentId,
                    Customer::ATTR_DEPTH => $newCustomerDepth,
                    Customer::ATTR_PATH => $newCustomerPath
                ];
                $updateRows = $this->_repoCustomer->updateById($customerId, $bind);
                if ($updateRows == 1) {
                    /* update depths and paths in downline */
                    $deltaDepth = $newCustomerDepth - $currDepth;
                    $pathKey = $currPath . $customerId . Cfg::DTPS;
                    $pathReplace = $newCustomerPath . $customerId . Cfg::DTPS;
                    $rowsUpdated = $this->_repoCustomer->updateChildrenPath($pathKey, $pathReplace, $deltaDepth);
                    $this->_logger->info("Total '$rowsUpdated' customers in downline were updated.");
                    /* save new record into change log */
                    $bind = [
                        Change::ATTR_CUSTOMER_ID => $customerId,
                        Change::ATTR_PARENT_ID => $newParentId,
                        Change::ATTR_DATE_CHANGED => $formatted

                    ];
                    $insertedId = $this->_repoChange->create($bind);
                    if ($insertedId) {
                        $this->_logger->info("New change log record #$insertedId is inserted (customer: $customerId, parent: $newParentId, date: $formatted).");
                        $this->_manTrans->commit($def);
                        $result->markSucceed();
                        $this->_logger->info("New parent #$newParentId for customer #$customerId is set.");
                    }
                }
            }
        } finally {
            $this->_manTrans->end($def);
        }
        return $result;
    }

    public function generateReferralCode(Request\GenerateReferralCode $request)
    {
        $result = new Response\GenerateReferralCode();
        $customerId = $request->getCustomerId();
        $humanRef = $request->getHumanRef();
        $this->_logger->info("Generate new code for customer #$customerId/$humanRef.");
        $code = ($humanRef) ? $humanRef : $customerId;
        $result->setReferralCode($code);
        $result->markSucceed();
        return $result;
    }
}