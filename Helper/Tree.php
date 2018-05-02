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
    /**
     * Convert array of array or data objects ([ 0 => [ 'id' => 321, ... ], ...])
     * to mapped array ([ 321 => [ 'id'=>321, ... ], ... ]).
     *
     * @param array|\Praxigento\Core\Data[] $tree nested array or array of data objects.
     * @param string $key name of the 'id' attribute.
     *
     * @return array|\Praxigento\Core\Data[]
     */
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

    /**
     * Create map of the front team members (siblings) [$custId => [$memberId, ...], ...] from compressed or snapshot
     * data.
     *
     * @param array|\Praxigento\Core\Data[] $data nested array or array of data objects.
     * @param string $keyCustId name of the 'customer id' attribute.
     * @param string $keyParentId name of the 'parent id' attribute.
     *
     * @return array [$custId => [$memberId, ...], ...]
     */
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

    /**
     * Get depth index for Downline Tree ordered by depth desc.
     *
     * @param array|\Praxigento\Core\Data[] $tree nested array or array of data objects.
     * @param string $keyCustId name of the 'customer id' attribute.
     * @param string $keyDepth name of the 'depth' attribute.
     *
     * @return array  [$depth => [$custId, ...]]
     */
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

    /**
     * Map IDs by groups for the mapping key:
     *  [[id=>123, path=>':1:', ...], [id=>321, path=>':1:', ...], ]
     *  [':1:'=> [123, 321, ...], ...]
     *
     * @param array|\Praxigento\Core\Data[] $data
     * @param string $keyId
     * @param string $keyMap
     * @return array
     */
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

    /**
     * Map 'value' elements of the $data array on $id elements.
     *
     * @param array|\Praxigento\Core\Data[] $data associative array with 'id' elements & 'value' elements.
     * @param string $keyId key for 'id' element
     * @param string $keyValue key for 'value' element
     * @return array [id => value, ...]
     */
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