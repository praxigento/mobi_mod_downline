<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Snap\Request;

/**
 * @method string getKeyCustomerId() name for key with customer ID data if third from of the tree is used.
 * @method void setKeyCustomerId(string $data)
 * @method string getKeyParentId() name for key with parent ID data if second or third from of the tree is used.
 * @method void setKeyParentId(string $data)
 * @method array getTree() [$custId => $parentId, ...] | [$custId => [KEY => $parentId, ...], ...] | [[KEY_C => $customerId, KEY_P => $parentId, ...], ...]
 * @method void setTree(array $data)
 */
class ExpandMinimal extends \Praxigento\Core\App\Service\Request {
}