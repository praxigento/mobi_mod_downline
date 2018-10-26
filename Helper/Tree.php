<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Snap;

/**
 * Integrate functionality to handle tree data.
 */
class Tree
    implements \Praxigento\Downline\Api\Helper\Tree
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

    public function expandMinimal($tree, $keyParent = null)
    {
        /**
         * Validate tree consistency: all parents should be customers too, create map for customers with invalid
         * parents to set this entries as orphans.
         */
        $mapOrphans = [];
        foreach ($tree as $customerId => $item) {
            $parentId = $this->getParentId($item, $keyParent);
            if (!isset($tree[$parentId])) {
                $mapOrphans[] = $customerId;
            }
        }
        /* create tree (see http://stackoverflow.com/questions/2915748/how-can-i-convert-a-series-of-parent-child-relationships-into-a-hierarchical-tre) */
        $flat = [];
        $treeExp = [];
        foreach ($tree as $customerId => $item) {
            $parentId = $this->getParentId($item, $keyParent);
            /* filter orphans */
            if (in_array($customerId, $mapOrphans)) {
                $parentId = $customerId;
            }
            /* map customers into tree */
            if (!isset($flat[$customerId])) {
                $flat[$customerId] = [];
            }
            if ($customerId != $parentId) {
                $flat[$parentId][$customerId] =& $flat[$customerId];
            } else {
                /* root node */
                $treeExp[$customerId] =& $flat[$customerId];
            }
        }
        /* populate tree with depth/path/... and compose array to insert into DB  */
        $result = [];
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

    public function getParentFullPath($path, $custId)
    {
        $result = $path . $custId . Cfg::DTPS;
        return $result;
    }

    public function mapById($tree, $key)
    {
        $result = [];
        foreach ($tree as $one) {
            /* $one should be an array ... */
            if (is_array($one)) {
                $id = $one[$key];
            } else {
                /* ... or stdClass with property  */
                if (isset($one->{$key})) {
                    $id = $one->{$key};
                } else {
                    /* ... or a DataObject */
                    $id = $one->get($key);
                }
            }
            $result[$id] = $one;
        }
        return $result;
    }

    public function mapByTeams($data, $keyCustId, $keyParentId)
    {
        $result = [];
        foreach ($data as $one) {
            if (is_array($one)) {
                $customerId = $one[$keyCustId];
                $parentId = $one[$keyParentId];
            } else {
                /* this should be data object */
                $customerId = $one->get($keyCustId);
                $parentId = $one->get($keyParentId);
            }
            if ($customerId == $parentId) {
                /* skip root nodes, root node is not a member of a team. */
                continue;
            }
            if (!isset($result[$parentId])) {
                $result[$parentId] = [];
            }
            $result[$parentId][] = $customerId;
        }
        return $result;
    }

    public function mapByTreeDepthAsc($tree, $keyCustId, $keyDepth)
    {
        $result = $this->mapByTreeDepthDesc($tree, $keyCustId, $keyDepth);
        $result = array_reverse($result);
        return $result;
    }

    public function mapByTreeDepthDesc($tree, $keyCustId, $keyDepth)
    {
        $result = [];
        foreach ($tree as $one) {
            if (is_array($one)) {
                $customerId = $one[$keyCustId];
                $depth = $one[$keyDepth];
            } else {
                /* this should be data object */
                $customerId = $one->get($keyCustId);
                $depth = $one->get($keyDepth);
            }
            if (!isset($result[$depth])) {
                $result[$depth] = [];
            }
            $result[$depth][] = $customerId;
        }
        /* sort by depth desc */
        krsort($result);
        return $result;
    }

    public function mapIdsByKey($data, $keyId, $keyMap)
    {
        $result = [];
        foreach ($data as $one) {
            if (is_array($one)) {
                $valueId = $one[$keyId];
                $valueMap = $one[$keyMap];
            } else {
                /* this should be data object */
                assert($one instanceof \Praxigento\Core\Data);
                $valueId = $one->get($keyId);
                $valueMap = $one->get($keyMap);
            }
            if (!isset($result[$valueMap])) {
                $result[$valueMap] = [];
            }
            $result[$valueMap][] = $valueId;
        }
        return $result;
    }

    public function mapValueById($data, $keyId, $keyValue)
    {
        $result = [];
        foreach ($data as $one) {
            /* $one should be an array or a DataObject */
            if (is_array($one)) {
                $id = $one[$keyId];
                $value = $one[$keyValue];
            } else {
                /* this should be a DataObject */
                $id = $one->get($keyId);
                $value = $one->get($keyValue);
            }
            $result[$id] = $value;
        }
        return $result;
    }

}