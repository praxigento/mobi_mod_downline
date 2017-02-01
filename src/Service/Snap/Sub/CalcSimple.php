<?php
/**
 * Simple in-memoty balance calculation.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap\Sub;


use Praxigento\Core\Tool\IPeriod;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Snap;

class CalcSimple
{
    /**
     * @var \Praxigento\Core\Tool\IPeriod
     */
    private $_toolPeriod;

    /**
     * CalcSimple constructor.
     */
    public function __construct(IPeriod $toolPeriod)
    {
        $this->_toolPeriod = $toolPeriod;
    }

    /**
     * Calculate downline snapshots by date basing on the last snapshot and change log.
     *
     * We use $currentState array to trace actual state during the changes. Target updates are placed in the $result.
     *
     * @param $currentState
     * @param $changes
     *
     * @return array
     */
    public function calcSnapshots($currentState, $changes)
    {
        $result = [];
        foreach ($changes as $downCustomer) {
            $customerId = $downCustomer[Change::ATTR_CUSTOMER_ID];
            $parentId = $downCustomer[Change::ATTR_PARENT_ID];
            $tsChanged = $downCustomer[Change::ATTR_DATE_CHANGED];
            $dsChanged = $this->_toolPeriod->getPeriodCurrent($tsChanged);
            /* $currentState contains actual state that is updated with changes */
            if (isset($currentState[$customerId])) {
                /* this is update of the existing customer */
                /* write down existing state */
                $currCustomer = $currentState[$customerId];
                $currDepth = $currCustomer[Snap::ATTR_DEPTH];
                $currPath = $currCustomer[Snap::ATTR_PATH];
                /* write down new state */
                if ($customerId == $parentId) {
                    /* this is root node customer */
                    $newDepth = Cfg::INIT_DEPTH;
                    $newPath = Cfg::DTPS;
                } else {
                    /* this is NOT root node customer */
                    $newParent = $currentState[$parentId];
                    $newDepth = $newParent[Snap::ATTR_DEPTH] + 1;
                    $newPath = $newParent[Snap::ATTR_PATH] . $parentId . Cfg::DTPS;
                }
                $customer = [
                    Snap::ATTR_DATE => $dsChanged,
                    Snap::ATTR_CUSTOMER_ID => $customerId,
                    Snap::ATTR_PARENT_ID => $parentId,
                    Snap::ATTR_DEPTH => $newDepth,
                    Snap::ATTR_PATH => $newPath
                ];
                /* we need to update downline's depths & paths for changed customer */
                /* TODO slow code, add ndx if too much slow */
                $key = $currPath . $customerId . Cfg::DTPS;
                $depthDelta = $newDepth - $currDepth;
                $pathReplace = $newPath . $customerId . Cfg::DTPS;
                foreach ($currentState as $downCustomer) {
                    $downPath = $downCustomer[Snap::ATTR_PATH];
                    if (false !== strrpos($downPath, $key, -strlen($downPath))) {
                        /* this is customer from downlilne, we need to change depth & path */
                        $downCustId = $downCustomer[Snap::ATTR_CUSTOMER_ID];
                        $downParentId = $downCustomer[Snap::ATTR_PARENT_ID];
                        $downNewDepth = $downCustomer[Snap::ATTR_DEPTH] + $depthDelta;
                        $downNewPath = str_replace($key, $pathReplace, $downCustomer[Snap::ATTR_PATH]);
                        $downCustomer[Snap::ATTR_DEPTH] = $downNewDepth;
                        $downCustomer[Snap::ATTR_PATH] = $downNewPath;
                        /* add to result updates */
                        $result[$dsChanged][$downCustId] = [
                            Snap::ATTR_DATE => $dsChanged,
                            Snap::ATTR_CUSTOMER_ID => $downCustId,
                            Snap::ATTR_PARENT_ID => $downParentId,
                            Snap::ATTR_DEPTH => $downNewDepth,
                            Snap::ATTR_PATH => $downNewPath
                        ];
                    }
                }
            } else {
                /* there is no data for this customer, this is new customer; just add new customer to results */
                if ($customerId == $parentId) {
                    /* this is root node customer */
                    $customer = [
                        Snap::ATTR_DATE => $dsChanged,
                        Snap::ATTR_CUSTOMER_ID => $customerId,
                        Snap::ATTR_PARENT_ID => $customerId,
                        Snap::ATTR_DEPTH => Cfg::INIT_DEPTH,
                        Snap::ATTR_PATH => Cfg::DTPS
                    ];
                } else {
                    /* this is NOT root node customer */
                    if (!isset($currentState[$parentId])) {
                        $yo = 'pta!';
                    }
                    $parent = $currentState[$parentId];
                    $customer = [
                        Snap::ATTR_DATE => $dsChanged,
                        Snap::ATTR_CUSTOMER_ID => $customerId,
                        Snap::ATTR_PARENT_ID => $parentId,
                        Snap::ATTR_DEPTH => $parent[Snap::ATTR_DEPTH] + 1,
                        Snap::ATTR_PATH => $parent[Snap::ATTR_PATH] . $parentId . Cfg::DTPS
                    ];
                }
            }
            $currentState[$customerId] = $customer;
            $result[$dsChanged][$customerId] = $customer;
        }
        return $result;
    }
}