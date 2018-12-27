<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Service\Snap\Calc\A;


use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Snap as ESnap;

class ComposeUpdates
{
    /** @var \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Downline\Api\Helper\Tree */
    private $hlpTree;

    /**
     * CalcSimple constructor.
     */
    public function __construct(
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Downline\Api\Helper\Tree $hlpTree
    ) {
        $this->hlpPeriod = $hlpPeriod;
        $this->hlpTree = $hlpTree;
    }

    /**
     * Calculate downline snapshots by date basing on the last snapshot and change log.
     *
     * We use $snap array to trace actual state during the changes. Target updates are placed in the $result.
     *
     * @param \Praxigento\Downline\Repo\Data\Snap[] $snap current snapshot (customer, parent, depth, path),
     *  see \Praxigento\Downline\Repo\Dao\Snap::getStateOnDate
     * @param \Praxigento\Downline\Repo\Data\Change[] $changes must be ordered by date_changed in asc
     *
     * @return array
     */
    public function exec($snap, $changes)
    {
        $result = [];
        /* We need to process changes one by one (changes are ordered by date) */
        /* to update downline paths/depths for changed customers */
        $mapByPath = $this->mapByPath($snap);
        foreach ($changes as $one) {
            $customerId = $one->getCustomerRef();
            $newParentId = $one->getParentRef();
            $tsChanged = $one->getDateChanged();
            $dsChanged = $this->hlpPeriod->getPeriodCurrent($tsChanged);
            /* $currentState contains actual state that is updated with changes */
            if (isset($snap[$customerId])) {
                /* this is update of the existing customer */
                /* write down existing state */
                /** @var ESnap $currCustomer */
                $currCustomer = $snap[$customerId];
                $currDepth = $currCustomer->getDepth();
                $currPath = $currCustomer->getPath();
                /* ... and compose new updated snap item */
                $customer = $this->composeSnapItem($customerId, $newParentId, $dsChanged, $snap);
                $snap[$customerId] = $customer;

                /* we need to update downline's depths & paths for changed customer */
                $key = $currPath . $customerId . Cfg::DTPS;
                $depthDelta = $customer->getDepth() - $currDepth;
                $pathUpdated = $customer->getPath();
                $pathReplace = $pathUpdated . $customerId . Cfg::DTPS;

                /* update path teams for changed customer */
                if (!isset($mapByPath[$currPath])) {
                    $mapByPath[$currPath] = [];
                }
                $teamCurr = &$mapByPath[$currPath]; // use & to work with nested array directly (not with copy of)
                /* we should remove changed customer from old parent's team */
                if (
                    is_array($teamCurr)
                    && (($keyToUnset = array_search($customerId, $teamCurr)) !== false)
                ) {
                    unset($teamCurr[$keyToUnset]);
                }
                /* ... and add changed customer to new parent team */
                $mapByPath[$pathUpdated][] = $customerId;
                /* update downline of the changed customer (on full depth) */
                foreach ($mapByPath as $path => $team) {
                    /* for all teams where path is started from old path */
                    $startsWith = (strpos($path, $key) === 0);
                    if ($startsWith) {
                        /* this is downline path for changed customer, we need to change depth & path for all customers inside */
                        foreach ($team as $memberId) {
                            /* get member one by one */
                            $member = $snap[$memberId];
                            $memberDepth = $member->getDepth();
                            $memberPath = $member->getPath();
                            /* change depth & path for the customer in changed downline */
                            $newDepth = $memberDepth + $depthDelta;
                            $newPath = str_replace($key, $pathReplace, $memberPath);
                            $member->setDepth($newDepth);
                            $member->setPath($newPath);
                            /* save changed customer in results */
                            $result[$dsChanged][$memberId] = $member;
                            /* register new team member */
                            $mapByPath[$newPath][] = $memberId;
                            /* update actual snap */
                            $snap[$memberId] = $member;
                        }
                        /* unset team for the current path from the map */
                        unset($mapByPath[$path]);
                    }
                }
            } else {
                /* there is no data for this customer, this is new customer; just add new customer to results */
                $customer = $this->composeSnapItem($customerId, $newParentId, $dsChanged, $snap);
                /* update actual snap */
                $snap[$customerId] = $customer;
                /* ... and paths registry */
                $pathNew = $customer->getPath();
                if (!isset($mapByPath[$pathNew])) {
                    $mapByPath[$pathNew] = [$customerId];
                } else {
                    $mapByPath[$pathNew][] = $customerId;
                }
            }
            $result[$dsChanged][$customerId] = $customer;
        }
        return $result;
    }

    /**
     * Compose snapshot item.
     *
     * @param int $customerId
     * @param int $parentId
     * @param string $dsChanged
     * @param ESnap[] $snap
     * @return ESnap
     */
    private function composeSnapItem($customerId, $parentId, $dsChanged, $snap)
    {
        $result = new ESnap();
        $result->setCustomerRef($customerId);
        $result->setParentRef($parentId);
        $result->setDate($dsChanged);
        if ($customerId == $parentId) {
            /* this is root node customer */
            $newDepth = Cfg::INIT_DEPTH;
            $newPath = Cfg::DTPS;
        } else {
            /* this is NOT root node customer */
            if (!isset($snap[$parentId])) {
                $breakPoint = 'inconsistency detected'; // this is code for debug only
            }
            /** @var ESnap $parent */
            $parent = $snap[$parentId];
            $newDepth = $parent->getDepth() + 1;
            $newPath = $parent->getPath() . $parentId . Cfg::DTPS;
        }
        $result->setDepth($newDepth);
        $result->setPath($newPath);
        return $result;
    }

    private function mapByPath($snap)
    {
        $result = $this->hlpTree->mapIdsByKey($snap, ESnap::A_CUSTOMER_REF, ESnap::A_PATH);
        return $result;
    }

}