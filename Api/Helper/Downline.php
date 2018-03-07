<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Helper;

/**
 * Downline Tree related operations.
 */
interface Downline
{
    /**
     * @param $tree array [$custId => $parentId, ...] | [$custId => [KEY => $parentId, ...], ...].
     * @param $keyParent string key for the $parentId if second form of the $tree is used.
     *
     * @return array [$custId=>[Snap::ATTR_CUSTOMER_ID, Snap::ATTR_PARENT_ID, Snap::ATTR_DEPTH, Snap::ATTR_PATH], ... ]
     */
    public function expandMinimal($tree, $keyParent = null);

    /**
     * @param $path string "/12/34/56/"
     *
     * @return array [12, 34, 56]
     */
    public function getParentsFromPath($path);

    /**
     * @param $path string "/12/34/56/"
     *
     * @return array [56, 34, 12]
     */
    public function getParentsFromPathReversed($path);
}