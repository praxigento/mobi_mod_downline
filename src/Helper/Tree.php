<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper;

/**
 * Integrate functionality to handle tree data.
 */
class Tree
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
            /* $one should be an array or a DataObject */
            $id = (is_array($one)) ? $one[$key] : $one->get($key);
            $result[$id] = $one;
        }
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
}