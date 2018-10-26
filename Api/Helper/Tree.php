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
     * Expand minimal tree data (customerId & parentId) to full structure (with depth & path).
     *
     * @param $tree array [$custId => $parentId, ...] | [$custId => [KEY => $parentId, ...], ...].
     * @param $keyParent string key for the $parentId if second form of the $tree is used.
     *
     * @return array [$custId=>[Snap::A_CUSTOMER_ID, Snap::A_PARENT_ID, Snap::A_DEPTH, Snap::A_PATH], ... ]
     */
    public function expandMinimal($tree, $keyParent = null);

    /**
     * Convert path to array of parents IDs.
     *
     * @param $path string ":12:34:56:"
     *
     * @return array [12, 34, 56]
     */
    public function getParentsFromPath($path);

    /**
     * Convert path to array of parents IDs in reverted order.
     *
     * @param $path string ":12:34:56:"
     *
     * @return array [56, 34, 12]
     */
    public function getParentsFromPathReversed($path);

    /**
     * Compose full path from $path & $custId to select all children from DB.
     *
     * @param string $path ":12:34:56:"
     * @param int $custId 78
     * @return string ":12:34:56:78:"
     */
    public function getParentFullPath($path, $custId);

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