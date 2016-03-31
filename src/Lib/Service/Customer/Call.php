<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Customer;

use Praxigento\Core\Lib\Service\Repo\Request\AddEntity as AddEntityRequest;
use Praxigento\Core\Lib\Service\Repo\Request\GetEntityByPk as GetEntityByPkRequest;
use Praxigento\Core\Lib\Service\Repo\Request\UpdateEntity as UpdateEntityRequest;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Lib\Entity\Change;
use Praxigento\Downline\Lib\Entity\Customer;
use Praxigento\Downline\Lib\Service\ICustomer;

class Call extends \Praxigento\Core\Lib\Service\Base\Call implements ICustomer {

    /**
     * Add new customer to downline and new entry to change log.
     *
     * @param Request\Add $request
     *
     * @return Response\Add
     */
    public function add(Request\Add $request) {
        $result = new Response\Add();
        $customerId = $request->getCustomerId();
        $parentId = $request->getParentId();
        $humanReference = $request->getReference();
        $countryCode = $request->getCountryCode();
        $date = $request->getDate();
        $this->_logger->info("Add new customer #$customerId with parent #$parentId to downline tree.");
        $this->_getConn()->beginTransaction();
        try {
            if($customerId == $parentId) {
                /* add root node */
                $this->_logger->info("This is root node (customer id is equal to parent id).");
                $path = Cfg::DTPS;
                $depth = Cfg::INIT_DEPTH;
            } else {
                /* get parent data by parent mage id */
                $reqByPk = new  GetEntityByPkRequest(Customer::ENTITY_NAME, [ Customer::ATTR_CUSTOMER_ID => $parentId ]);
                $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
                $parentPath = $respByPk->getData(Customer::ATTR_PATH);
                $parentDepth = $respByPk->getData(Customer::ATTR_DEPTH);
                $path = $parentPath . $parentId . Cfg::DTPS;
                $depth = $parentDepth + 1;
            }
            /* add customer to downline */
            $toAdd = [
                Customer::ATTR_CUSTOMER_ID  => $customerId,
                Customer::ATTR_PARENT_ID    => $parentId,
                Customer::ATTR_DEPTH        => $depth,
                Customer::ATTR_PATH         => $path,
                Customer::ATTR_COUNTRY_CODE => $countryCode
            ];
            if(isset($humanReference)) {
                $toAdd[Customer::ATTR_HUMAN_REF] = $humanReference;
            }
            $reqAdd = new  AddEntityRequest(Customer::ENTITY_NAME, $toAdd);
            $respAdd = $this->_callRepo->addEntity($reqAdd);
            if($respAdd->isSucceed()) {
                /* save log record to changes registry */
                $formatted = $date;
                $toLog = [
                    Change::ATTR_CUSTOMER_ID  => $customerId,
                    Change::ATTR_PARENT_ID    => $parentId,
                    Change::ATTR_DATE_CHANGED => $formatted
                ];
                $reqLog = new  AddEntityRequest(Change::ENTITY_NAME, $toLog);
                $respLog = $this->_callRepo->addEntity($reqLog);
                if($respLog->isSucceed()) {
                    $this->_logger->debug("Downline changes are logged in registry with date: $formatted.");
                    $idInserted = $respLog->getIdInserted();
                    $this->_logger->debug("New change log record #$idInserted is inserted (customer: $customerId, parent: $parentId, date: $formatted).");
                    $result->setData($toAdd);
                    $result->setAsSucceed();
                    $this->_getConn()->commit();
                    $this->_logger->info("New customer #$customerId with parent #$parentId is added to downline tree.");
                } else {
                    $this->_getConn()->rollBack();
                    $this->_logger->error("Cannot add new customer to downline. Insert of the change log is failed.");
                }
            } else {
                $this->_getConn()->rollBack();
                $this->_logger->error("Cannot add new customer to downline. Insert of the customer is failed.");
            }
        } catch(\Exception $e) {
            $this->_getConn()->rollback();
            $this->_logger->error("Cannot add new customer to downline. Exception: " . $e->getMessage());
        }
        return $result;
    }


    public function changeParent(Request\ChangeParent $request) {
        $result = new Response\ChangeParent();
        $customerId = $request->getData(Request\ChangeParent::CUSTOMER_ID);
        $newParentId = $request->getData(Request\ChangeParent::PARENT_ID_NEW);
        $formatted = $request->getData(Request\ChangeParent::DATE);
        $this->_logger->info("Set up new parent #$newParentId for customer #$customerId.");
        $this->_getConn()->beginTransaction();
        try {
            /* get customer data */
            $reqByPk = new  GetEntityByPkRequest(Customer::ENTITY_NAME, [ Customer::ATTR_CUSTOMER_ID => $customerId ]);
            $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
            $currParentId = $respByPk->getData(Customer::ATTR_PARENT_ID);
            $currDepth = $respByPk->getData(Customer::ATTR_DEPTH);
            $currPath = $respByPk->getData(Customer::ATTR_PATH);
            if($currParentId == $newParentId) {
                /* nothing to change */
                $result->setAsSucceed();
                $this->_getConn()->commit();
                $this->_logger->notice("Current parent is the same as new one. Nothing to do.");
            } else {
                if($customerId == $newParentId) {
                    /* change to root node */
                    $newCustomerDepth = Cfg::INIT_DEPTH;
                    $newCustomerPath = Cfg::DTPS;
                } else {
                    /* get new parent data */
                    $reqByPk->pk[Customer::ATTR_CUSTOMER_ID] = $newParentId;
                    $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
                    $newParentDepth = $respByPk->getData(Customer::ATTR_DEPTH);
                    $newParentPath = $respByPk->getData(Customer::ATTR_PATH);
                    $newCustomerDepth = $newParentDepth + 1;
                    $newCustomerPath = $newParentPath . $newParentId . Cfg::DTPS;
                }
                /* update customer with new data */
                $bind = [
                    Customer::ATTR_PARENT_ID => $newParentId,
                    Customer::ATTR_DEPTH     => $newCustomerDepth,
                    Customer::ATTR_PATH      => $newCustomerPath
                ];
                $where = Customer::ATTR_CUSTOMER_ID . '=' . $customerId;
                $reqUpdate = new UpdateEntityRequest(Customer::ENTITY_NAME, $bind, $where);
                $respUpdate = $this->_callRepo->updateEntity($reqUpdate);
                if($respUpdate->isSucceed() && $respUpdate->getRowsUpdated() == 1) {
                    /* update depths and paths in downline */
                    $deltaDepth = $newCustomerDepth - $currDepth;
                    $pathKey = $currPath . $customerId . Cfg::DTPS;
                    $pathReplace = $newCustomerPath . $customerId . Cfg::DTPS;
                    $quotedKey = $this->_getConn()->quote($pathKey);
                    $quotedReplace = $this->_getConn()->quote($pathReplace);
                    $reqUpdate->bind = [
                        Customer::ATTR_DEPTH => Customer::ATTR_DEPTH . '+' . $deltaDepth,
                        Customer::ATTR_PATH  => 'REPLACE(' . Customer::ATTR_PATH . ", $quotedKey, $quotedReplace)"
                    ];
                    $cond = $this->_getConn()->quote($pathKey . '%');
                    $reqUpdate->where = Customer::ATTR_PATH . " LIKE $cond";
                    $respUpdate = $this->_callRepo->updateEntity($reqUpdate);
                    $rowsUpdated = $respUpdate->getRowsUpdated();
                    $this->_logger->info("Total '$rowsUpdated' customers in downline were updated.");
                    /* save new record into change log */
                    $bind = [
                        Change::ATTR_CUSTOMER_ID  => $customerId,
                        Change::ATTR_PARENT_ID    => $newParentId,
                        Change::ATTR_DATE_CHANGED => $formatted

                    ];
                    $reqAdd = new AddEntityRequest(Change::ENTITY_NAME, $bind);
                    $respAdd = $this->_callRepo->addEntity($reqAdd);
                    if($respAdd->isSucceed()) {
                        $insertedId = $respAdd->getIdInserted();
                        $this->_logger->info("New change log record #$insertedId is inserted (customer: $customerId, parent: $newParentId, date: $formatted).");
                        $this->_getConn()->commit();
                        $result->setAsSucceed();
                        $this->_logger->info("New parent #$newParentId for customer #$customerId is set.");
                    }
                } else {
                    $this->_getConn()->rollBack();
                    $this->_logger->error("Cannot update parent for customer #$customerId. New parent #$newParentId is not set.");
                }
            }
        } catch(\Exception $e) {
            $this->_getConn()->rollBack();
            $this->_logger->error("Cannot set new parent #$newParentId for customer #$customerId. Exception: " . $e->getMessage());
        }
        return $result;
    }

}