<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Customer;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Lib\Service\ICustomer;

class Call extends \Praxigento\Core\Service\Base\Call implements ICustomer
{
    /** @var \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoBasic;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($logger);
        $this->_manTrans = $manTrans;
        $this->_repoBasic = $repoGeneric;
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
        $trans = $this->_manTrans->transactionBegin();
        try {
            if ($customerId == $parentId) {
                /* add root node */
                $this->_logger->info("This is root node (customer id is equal to parent id).");
                $path = Cfg::DTPS;
                $depth = Cfg::INIT_DEPTH;
            } else {
                /* get parent data by parent Mage id */
                $data = $this->_repoBasic->getEntityByPk(
                    Customer::ENTITY_NAME,
                    [Customer::ATTR_CUSTOMER_ID => $parentId]
                );
                $parentPath = $data[Customer::ATTR_PATH];
                $parentDepth = $data[Customer::ATTR_DEPTH];
                $path = $parentPath . $parentId . Cfg::DTPS;
                $depth = $parentDepth + 1;

            }
            /* add customer to downline */
            $toAdd = [
                Customer::ATTR_CUSTOMER_ID => $customerId,
                Customer::ATTR_PARENT_ID => $parentId,
                Customer::ATTR_DEPTH => $depth,
                Customer::ATTR_PATH => $path,
                Customer::ATTR_COUNTRY_CODE => $countryCode
            ];
            if (isset($humanReference)) {
                $toAdd[Customer::ATTR_HUMAN_REF] = $humanReference;
            }
            $this->_repoBasic->addEntity(Customer::ENTITY_NAME, $toAdd);

            /* save log record to changes registry */
            $formatted = $date;
            $toLog = [
                Change::ATTR_CUSTOMER_ID => $customerId,
                Change::ATTR_PARENT_ID => $parentId,
                Change::ATTR_DATE_CHANGED => $formatted
            ];
            $idLog = $this->_repoBasic->addEntity(Change::ENTITY_NAME, $toLog);
            if ($idLog) {
                $this->_logger->debug("Downline changes are logged in registry with date: $formatted.");
                $this->_logger->debug("New change log record #$idLog is inserted (customer: $customerId, parent: $parentId, date: $formatted).");
                $result->setData($toAdd);
                $result->markSucceed();
                $this->_manTrans->transactionCommit($trans);
                $this->_logger->info("New customer #$customerId with parent #$parentId is added to downline tree.");
            } else {
                $this->_logger->error("Cannot add new customer to downline. Insert of the change log is failed.");
            }
        } finally {
            $this->_manTrans->transactionClose($trans);
        }

        return $result;
    }


    public function changeParent(Request\ChangeParent $request)
    {
        $result = new Response\ChangeParent();
        $customerId = $request->getData(Request\ChangeParent::CUSTOMER_ID);
        $newParentId = $request->getData(Request\ChangeParent::PARENT_ID_NEW);
        $formatted = $request->getData(Request\ChangeParent::DATE);
        $this->_logger->info("Set up new parent #$newParentId for customer #$customerId.");
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* get customer data */
            $data = $this->_repoBasic->getEntityByPk(
                Customer::ENTITY_NAME,
                [Customer::ATTR_CUSTOMER_ID => $customerId]
            );
            $currParentId = $data[Customer::ATTR_PARENT_ID];
            $currDepth = $data[Customer::ATTR_DEPTH];
            $currPath = $data[Customer::ATTR_PATH];
            if ($currParentId == $newParentId) {
                /* nothing to change */
                $result->markSucceed();
                $this->_manTrans->transactionCommit($trans);
                $this->_logger->notice("Current parent is the same as new one. Nothing to do.");
            } else {
                if ($customerId == $newParentId) {
                    /* change to root node */
                    $newCustomerDepth = Cfg::INIT_DEPTH;
                    $newCustomerPath = Cfg::DTPS;
                } else {
                    /* get new parent data */
                    $newParentData = $this->_repoBasic->getEntityByPk(
                        Customer::ENTITY_NAME,
                        [Customer::ATTR_CUSTOMER_ID => $newParentId]
                    );
                    $newParentDepth = $newParentData[Customer::ATTR_DEPTH];
                    $newParentPath = $newParentData[Customer::ATTR_PATH];
                    $newCustomerDepth = $newParentDepth + 1;
                    $newCustomerPath = $newParentPath . $newParentId . Cfg::DTPS;
                }
                /* update customer with new data */
                $bind = [
                    Customer::ATTR_PARENT_ID => $newParentId,
                    Customer::ATTR_DEPTH => $newCustomerDepth,
                    Customer::ATTR_PATH => $newCustomerPath
                ];
                $where = Customer::ATTR_CUSTOMER_ID . '=' . (int)$customerId;
                $updateRows = $this->_repoBasic->updateEntity(Customer::ENTITY_NAME, $bind, $where);
                if ($updateRows == 1) {
                    /* TODO: this functionality should be placed in the repo class (with parameters quoting: $this->_getConn()->quote($pathKey);)*/
//                    $quotedKey = $this->_getConn()->quote($pathKey);
//                    $quotedReplace = $this->_getConn()->quote($pathReplace);
//                    $cond = $this->_getConn()->quote($pathKey . '%');
                    /* update depths and paths in downline */
                    $deltaDepth = $newCustomerDepth - $currDepth;
                    $pathKey = $currPath . $customerId . Cfg::DTPS;
                    $pathReplace = $newCustomerPath . $customerId . Cfg::DTPS;
                    $bind = [
                        Customer::ATTR_DEPTH => Customer::ATTR_DEPTH . '+' . $deltaDepth,
                        Customer::ATTR_PATH => 'REPLACE(' . Customer::ATTR_PATH . ", $pathKey, $pathReplace)"
                    ];
                    $where = Customer::ATTR_PATH . " LIKE '$pathKey%'";
                    $rowsUpdated = $this->_repoBasic->updateEntity(Customer::ENTITY_NAME, $bind, $where);
                    $this->_logger->info("Total '$rowsUpdated' customers in downline were updated.");
                    /* save new record into change log */
                    $bind = [
                        Change::ATTR_CUSTOMER_ID => $customerId,
                        Change::ATTR_PARENT_ID => $newParentId,
                        Change::ATTR_DATE_CHANGED => $formatted

                    ];
                    $insertedId = $this->_repoBasic->addEntity(Change::ENTITY_NAME, $bind);
                    if ($insertedId) {
                        $this->_logger->info("New change log record #$insertedId is inserted (customer: $customerId, parent: $newParentId, date: $formatted).");
                        $this->_manTrans->transactionCommit($trans);
                        $result->markSucceed();
                        $this->_logger->info("New parent #$newParentId for customer #$customerId is set.");
                    }
                }
            }
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }

}