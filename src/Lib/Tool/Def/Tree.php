<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Lib\Tool\Def;


use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Lib\Entity\Snap;
use Praxigento\Downline\Lib\Tool\ITree;

class Tree implements ITree {

    public function getParentsFromPath($path) {
        $result = explode(Cfg::DTPS, $path);
        /* remove empty elements from begin of the array */
        if(strlen($result[0]) == 0) {
            array_shift($result);
        }
        /* remove empty elements from end of the array */
        if(strlen($result[count($result) - 1]) == 0) {
            array_pop($result);
        }
        return $result;
    }

    public function getParentsFromPathReversed($path) {
        $result = array_reverse($this->getParentsFromPath($path));
        return $result;
    }

    /**
     * @param $tree array [$custId => $parentId, ...] | [$custId => [KEY => $parentId, ...], ...].
     * @param $keyParent string key for the $parentId if second form of the $tree is used.
     *
     * @return array [$custId=>[Snap::ATTR_CUSTOMER_ID, Snap::ATTR_PARENT_ID, Snap::ATTR_DEPTH, Snap::ATTR_PATH], ... ]
     */
    public function expandMinimal($tree, $keyParent = null) {
        /**
         * Validate tree consistency: all parents should be customers too, create map for customers with invalid
         * parents to set this entries as orphans.
         */
        $mapOrphans = [ ];
        foreach($tree as $customerId => $item) {
            /* get parentId by first or second variant */
            $parentId = !is_array($item) ? $item : $item[$keyParent];
            if(!isset($tree[$parentId])) {
                $mapOrphans[] = $customerId;
            }
        }
        /* create tree (see http://stackoverflow.com/questions/2915748/how-can-i-convert-a-series-of-parent-child-relationships-into-a-hierarchical-tre) */
        $flat = [ ];
        $treeExp = [ ];
        foreach($tree as $customerId => $item) {
            /* get parentId by first or second variant */
            $parentId = !is_array($item) ? $item : $item[$keyParent];
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
        $this->_composeSnapData($result, $treeExp);
        return $result;
    }

    /**
     * Recursively compose $result as array of elements:
     * [
     *  Snap::ATTR_CUSTOMER_ID,
     *  Snap::ATTR_PARENT_ID,
     *  Snap::ATTR_DEPTH,
     *  Snap::ATTR_PATH
     * ]
     *
     * @param      $result
     * @param      $tree
     * @param null $parentId
     */
    private function _composeSnapData(&$result, $tree, $parentId = null) {
        foreach($tree as $custId => $children) {
            if(is_null($parentId)) {
                /* this is root node */
                $result[$custId] = [
                    Snap::ATTR_CUSTOMER_ID => $custId,
                    Snap::ATTR_PARENT_ID   => $custId,
                    Snap::ATTR_DEPTH       => Cfg::INIT_DEPTH,
                    Snap::ATTR_PATH        => Cfg::DTPS
                ];
            } else {
                $parentData = $result[$parentId];
                $result[$custId] = [
                    Snap::ATTR_CUSTOMER_ID => $custId,
                    Snap::ATTR_PARENT_ID   => $parentId,
                    Snap::ATTR_DEPTH       => $parentData[Snap::ATTR_DEPTH] + 1,
                    Snap::ATTR_PATH        => $parentData[Snap::ATTR_PATH] . $parentId . Cfg::DTPS
                ];
            }
            if(sizeof($children) > 0) {
                $this->_composeSnapData($result, $children, $custId);
            }
        }
    }
}