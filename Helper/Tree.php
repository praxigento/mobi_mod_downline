<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper;

/**
 * Integrate functionality to handle tree data.
 */
class Tree
    implements \Praxigento\Downline\Api\Helper\Tree
{
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