<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Api\Helper;


/**
 * Integrate functionality to handle tree data.
 */
interface Tree
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
    public function mapById($tree, $key);

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
    public function mapByTeams($data, $keyCustId, $keyParentId);

    /**
     * Get depth index for Downline Tree ordered by depth asc.
     *
     * @param array|\Praxigento\Core\Data[] $tree nested array or array of data objects.
     * @param string $keyCustId name of the 'customer id' attribute.
     * @param string $keyDepth name of the 'depth' attribute.
     *
     * @return array  [$depth => [$custId, ...]]
     */
    public function mapByTreeDepthAsc($tree, $keyCustId, $keyDepth);

    /**
     * Get depth index for Downline Tree ordered by depth desc.
     *
     * @param array|\Praxigento\Core\Data[] $tree nested array or array of data objects.
     * @param string $keyCustId name of the 'customer id' attribute.
     * @param string $keyDepth name of the 'depth' attribute.
     *
     * @return array  [$depth => [$custId, ...]]
     */
    public function mapByTreeDepthDesc($tree, $keyCustId, $keyDepth);

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
    public function mapIdsByKey($data, $keyId, $keyMap);

    /**
     * Map 'value' elements of the $data array on $id elements.
     *
     * @param array|\Praxigento\Core\Data[] $data associative array with 'id' elements & 'value' elements.
     * @param string $keyId key for 'id' element
     * @param string $keyValue key for 'value' element
     * @return array [id => value, ...]
     */
    public function mapValueById($data, $keyId, $keyValue);
}