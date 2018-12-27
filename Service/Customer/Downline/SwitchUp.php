<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Downline;

use Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp\Response as AResponse;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Change as EDwnlChange;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

/**
 * Switch customer's downline to customer's parent (exclude unqualified customer from the game).
 */
class SwitchUp
    implements \Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp
{
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    private $daoDwnlChange;
    /** @var  \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Repo\Dao\Change $daoDwnlChange,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Core\Api\Helper\Date $hlpDate
    ) {
        $this->logger = $logger;
        $this->daoDwnlChange = $daoDwnlChange;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->hlpDate = $hlpDate;
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
        $formatted = $req->getDate();
        if (empty($formatted)) {
            $formatted = $this->hlpDate->getUtcNowForDb();
        }
        $this->logger->info("Downline for customer #$customerId will be switched to it's parent.");

        /** perform processing */
        /* get customer downline data */
        $customer = $this->daoDwnlCust->getById($customerId);
        $parentId = $customer->getParentRef();
        $path = $customer->getPath();

        /* I skip the rare case when customer is the root node (w/o parent) */

        /* get customer's first-line team */
        $where = EDwnlCust::A_PARENT_REF . '=' . (int)$customerId;
        $firstLine = $this->daoDwnlCust->get($where);

        if (count($firstLine)) {
            /* update path & depth for all customers in the switched downline */
            $pathToReplace = $path . $customerId . Cfg::DTPS;
            $updated = $this->daoDwnlCust->updateChildrenPath($pathToReplace, $path, -1);
            $this->logger->info("Path is updated for '$updated' downline customers ($pathToReplace).");

            /* change parent for customer's first-line team */
            $bind = [
                EDwnlCust::A_PARENT_REF => $parentId
            ];
            $updated = $this->daoDwnlCust->update($bind, $where);
            $this->logger->info("Parent is updated for '$updated' downline customers from the first line.");

            /* log downline changes */
            /** @var EDwnlCust $item */
            foreach ($firstLine as $item) {
                $custIdChange = $item->getCustomerRef();
                $change = new EDwnlChange();
                $change->setCustomerRef($custIdChange);
                $change->setParentRef($parentId);
                $change->setDateChanged($formatted);
                $this->daoDwnlChange->create($change);
            }
        }

        /** compose result */
        $result = new AResponse();
        $result->markSucceed();
        return $result;
    }

}