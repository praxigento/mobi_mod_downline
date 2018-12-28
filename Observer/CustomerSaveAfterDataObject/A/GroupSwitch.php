<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Observer\CustomerSaveAfterDataObject\A;

use Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp\Request as ASwitchUpRequest;
use Praxigento\Downline\Repo\Data\Change\Group as EChangeGroup;
use Praxigento\Downline\Service\Customer\Downline\StickUp\Request as AStickUpRequest;

/**
 * Analyze customer group change, restructure downline tree and registry group change event.
 */
class GroupSwitch
{
    /** @var \Praxigento\Downline\Repo\Dao\Change\Group */
    private $daoChangeGroup;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Praxigento\Downline\Api\Helper\Group\Transition */
    private $hlpGroupTrans;
    /** @var \Praxigento\Downline\Service\Customer\Downline\StickUp */
    private $servStickUp;
    /** @var \Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp */
    private $servSwitchUp;

    public function __construct(
        \Praxigento\Downline\Repo\Dao\Change\Group $daoChangeGroup,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Downline\Api\Helper\Group\Transition $hlpGroupTrans,
        \Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp $servSwitchUp,
        \Praxigento\Downline\Service\Customer\Downline\StickUp $servStickUp
    ) {
        $this->daoChangeGroup = $daoChangeGroup;
        $this->hlpDate = $hlpDate;
        $this->hlpGroupTrans = $hlpGroupTrans;
        $this->servSwitchUp = $servSwitchUp;
        $this->servStickUp = $servStickUp;
    }

    /**
     * @param int $groupIdBefore
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @throws \Exception
     */
    public function exec($groupIdBefore, $customer)
    {
        $groupIdAfter = $customer->getGroupId();
        if ($groupIdBefore != $groupIdAfter) {
            $custId = $customer->getId();
            $this->switchDownline($custId, $groupIdBefore, $groupIdAfter);
            $this->registerGroupChange($custId, $groupIdBefore, $groupIdAfter);
        }
    }

    /**
     * Save group change event into registry.
     *
     * @param int $custId
     * @param int $groupIdOld
     * @param int $groupIdNew
     */
    private function registerGroupChange($custId, $groupIdOld, $groupIdNew)
    {
        $data = new EChangeGroup();
        $data->setCustomerRef($custId);
        $data->setGroupOld($groupIdOld);
        $data->setGroupNew($groupIdNew);
        $dateChanged = $this->hlpDate->getUtcNowForDb();
        $data->setDateChanged($dateChanged);
        $this->daoChangeGroup->create($data);
    }

    /**
     * Change downline on group upgrade/downgrade event.
     *
     * @param int $custId
     * @param int $groupIdBefore
     * @param int $groupIdAfter
     * @throws \Exception
     */
    private function switchDownline($custId, $groupIdBefore, $groupIdAfter)
    {
        $isDowngrade = $this->hlpGroupTrans->isDowngrade($groupIdBefore, $groupIdAfter);
        if ($isDowngrade) {
            /* we need to switch all customer's children to the customer's parent */
            $req = new ASwitchUpRequest();
            $req->setCustomerId($custId);
            $this->servSwitchUp->exec($req);
        } else {
            $isUpgrade = $this->hlpGroupTrans->isUpgrade($groupIdBefore, $groupIdAfter);
            if ($isUpgrade) {
                /* we need to place customer under the first distributor in upline */
                $req = new AStickUpRequest();
                $req->setCustomerId($custId);
                $this->servStickUp->exec($req);
            }
        }
    }
}