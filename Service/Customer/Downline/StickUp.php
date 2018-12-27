<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Downline;

use Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp\Response as AResponse;
use Praxigento\Downline\Api\Service\Customer\Parent\Change\Request as AChangeRequest;
use Praxigento\Downline\Api\Service\Customer\Parent\Change\Response as AChangeResponse;

/**
 * Move customer to the closest distributor in upline on upgrade (skip regular customers).
 */
class StickUp
{
    /** @var  \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Api\Helper\Config */
    private $hlpCfgDwnl;
    /** @var \Praxigento\Core\Api\Helper\Customer\Group */
    private $hlpCustGroup;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Praxigento\Downline\Api\Helper\Tree */
    private $hlpTree;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Downline\Api\Service\Customer\Parent\Change */
    private $servCustChange;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Core\Api\Helper\Customer\Group $hlpCustGroup,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Downline\Api\Helper\Config $hlpCfgDwnl,
        \Praxigento\Downline\Api\Helper\Tree $hlpTree,
        \Praxigento\Downline\Api\Service\Customer\Parent\Change $servCustChange
    ) {
        $this->logger = $logger;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->hlpCustGroup = $hlpCustGroup;
        $this->hlpDate = $hlpDate;
        $this->hlpCfgDwnl = $hlpCfgDwnl;
        $this->hlpTree = $hlpTree;
        $this->servCustChange = $servCustChange;
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
        $this->logger->info("Customer #$customerId will be switched up to the first distributor.");

        /** perform processing */
        /* get customer downline data */
        $customer = $this->daoDwnlCust->getById($customerId);
        $parentId = $customer->getParentRef();
        $path = $customer->getPath();

        if ($parentId != $customerId) {
            /* distributors groups */
            $distrGroups = $this->hlpCfgDwnl->getDowngradeGroupsDistrs();
            $parts = $this->hlpTree->getParentsFromPathReversed($path);
            $found = null;
            foreach ($parts as $part) {
                $groupId = $this->hlpCustGroup->getIdByCustomerId($part);
                if (in_array($groupId, $distrGroups)) {
                    $found = $part;
                    break;
                }
            }
            if (!$found) {
                /* all parents are not distributors, switch under itself (root node) */
                $found = $customerId;
            }
            if ($found != $parentId) {
                $reqChange = new AChangeRequest();
                $reqChange->setCustomerId($customerId);
                $reqChange->setNewParentId($found);
                $reqChange->setDate($formatted);
                /** @var AChangeResponse $respChange */
                $respChange = $this->servCustChange->exec($reqChange);
            } else {
                $this->logger->info("Customer #$customerId already has distributor as a parent (#$parentId).");
            }
        } else {
            /* skip processing for tree's root nodes */
            $this->logger->info("Customer #$customerId is a root node and has no parents.");
        }

        /** compose result */
        $result = new AResponse();
        $result->markSucceed();
        return $result;
    }

}