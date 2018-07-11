<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\ChangeParent\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\ChangeParent\Response as AResponse;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Change as EDwnlChange;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

/**
 * Add customer to downline and new entry to change log.
 */
class ChangeParent
    implements \Praxigento\Downline\Api\Service\Customer\ChangeParent
{
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    private $daoDwnlChange;
    /** @var  \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Repo\Dao\Change $daoDwnlChange,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust
    ) {
        $this->logger = $logger;
        $this->daoDwnlChange = $daoDwnlChange;
        $this->daoDwnlCust = $daoDwnlCust;
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
        $newParentId = $req->getNewParentId();
        $formatted = $req->getDate();
        $this->logger->info("Set up new parent #$newParentId for customer #$customerId.");

        /** perform processing */
        /* get customer's downline  data */
        $data = $this->daoDwnlCust->getById($customerId);
        $currParentId = $data->getParentId();;
        $currDepth = $data->getDepth();
        $currPath = $data->getPath();

        if ($currParentId == $newParentId) {
            /* nothing to change */
            $this->logger->notice("Current parent is the same as new one. Nothing to do.");
        } else {
            if ($customerId == $newParentId) {
                /* change to root node */
                $newCustomerDepth = Cfg::INIT_DEPTH;
                $newCustomerPath = Cfg::DTPS;
            } else {
                /* get new parent data */
                $newParentData = $this->daoDwnlCust->getById($newParentId);
                $newParentDepth = $newParentData->getDepth();
                $newParentPath = $newParentData->getPath();
                $newCustomerDepth = $newParentDepth + 1;
                $newCustomerPath = $newParentPath . $newParentId . Cfg::DTPS;
            }
            /* update customer with new data */
            $update = new EDwnlCust();
            $update->setParentId($newParentId);
            $update->setDepth($newCustomerDepth);
            $update->setPath($newCustomerPath);
            $this->daoDwnlCust->updateById($customerId, $update);
            /* update depths and paths in downline */
            $deltaDepth = $newCustomerDepth - $currDepth;
            $pathKey = $currPath . $customerId . Cfg::DTPS;
            $pathReplace = $newCustomerPath . $customerId . Cfg::DTPS;
            $rowsUpdated = $this->daoDwnlCust->updateChildrenPath($pathKey, $pathReplace, $deltaDepth);
            $this->logger->info("Total '$rowsUpdated' customers in downline were updated.");
            /* save new record into change log */
            $change = new EDwnlChange();
            $change->setCustomerId($customerId);
            $change->setParentId($newParentId);
            $change->setDateChanged($formatted);
            $insertedId = $this->daoDwnlChange->create($change);
            $this->logger->info("New change log record #$insertedId is inserted (customer: $customerId, parent: $newParentId, date: $formatted).");
        }

        /** compose result */
        $result = new AResponse();
        $result->markSucceed();
        return $result;
    }

}