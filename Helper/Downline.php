<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper;


use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Snap;

class Downline
    implements \Praxigento\Downline\Api\Helper\Downline
{

    /**
     * Recursively compose $result as array of elements:
     * [
     *  Snap::A_CUSTOMER_ID,
     *  Snap::A_PARENT_ID,
     *  Snap::A_DEPTH,
     *  Snap::A_PATH
     * ]
     *
     * @param      $result
     * @param      $tree
     * @param null $parentId
     */
    private function composeSnapData(&$result, $tree, $parentId = null)
    {
        foreach ($tree as $custId => $children) {
            if (is_null($parentId)) {
                /* this is root node */
                $result[$custId] = [
                    Snap::A_CUSTOMER_ID => $custId,
                    Snap::A_PARENT_ID => $custId,
                    Snap::A_DEPTH => Cfg::INIT_DEPTH,
                    Snap::A_PATH => Cfg::DTPS
                ];
            } else {
                $parentData = $result[$parentId];
                $result[$custId] = [
                    Snap::A_CUSTOMER_ID => $custId,
                    Snap::A_PARENT_ID => $parentId,
                    Snap::A_DEPTH => $parentData[Snap::A_DEPTH] + 1,
                    Snap::A_PATH => $parentData[Snap::A_PATH] . $parentId . Cfg::DTPS
                ];
            }
            if (sizeof($children) > 0) {
                $this->composeSnapData($result, $children, $custId);
            }
        }
    }

    /**
     * @param $tree array [$custId => $parentId, ...] | [$custId => [KEY => $parentId, ...], ...].
     * @param $keyParent string key for the $parentId if second form of the $tree is used.
     *
     * @return array [$custId=>[Snap::A_CUSTOMER_ID, Snap::A_PARENT_ID, Snap::A_DEPTH, Snap::A_PATH], ... ]
     */
    public function expandMinimal($tree, $keyParent = null) {
        /**
         * Validate tree consistency: all parents should be customers too, create map for customers with invalid
         * parents to set this entries as orphans.
         */
        $mapOrphans = [ ];
        foreach($tree as $customerId => $item) {
            $parentId = $this->getParentId($item, $keyParent);
            if(!isset($tree[$parentId])) {
                $mapOrphans[] = $customerId;
            }
        }
        /* create tree (see http://stackoverflow.com/questions/2915748/how-can-i-convert-a-series-of-parent-child-relationships-into-a-hierarchical-tre) */
        $flat = [ ];
        $treeExp = [ ];
        foreach($tree as $customerId => $item) {
            $parentId = $this->getParentId($item, $keyParent);
            /* filter orphans */
            if(in_array($customerId, $mapOrphans)) {
                $parentId = $customerId;
            }
            /* map customers into tree */
            if(!isset($flat[$customerId])) {
                $flat[$customerId] = [ ];
            }
            if($customerId != $parentId) {
                $flat[$parentId][$customerId] =& $flat[$customerId];
            } else {
                /* root node */
                $treeExp[$customerId] =& $flat[$customerId];
            }
        }
        /* populate tree with depth/path/... and compose array to insert into DB  */
        $result = [ ];
        $this->composeSnapData($result, $treeExp);
        return $result;
    }

    /**
     * Get Parent ID from entry. Entry can be integer, array or Data Object.
     *
     * @param int|array|\Praxigento\Core\Data $item
     * @param string $key
     * @return int
     */
    private function getParentId($item, $key)
    {
        if (is_array($item)) {
            $result = $item[$key];
        } elseif ($item instanceof \Praxigento\Core\Data) {
            $result = $item->get($key);
        } else {
            $result = $item;
        }
        return $result;
    }

    public function getParentsFromPath($path)
    {
        $result = explode(Cfg::DTPS, $path);
        /* remove empty elements from begin of the array */
        if (strlen($result[0]) == 0) {
            array_shift($result);
        }
        /* remove empty elements from end of the array */
        if (strlen($result[count($result) - 1]) == 0) {
            array_pop($result);
        }
        return $result;
    }

    public function getParentsFromPathReversed($path)
    {
        $result = array_reverse($this->getParentsFromPath($path));
        return $result;
    }
}