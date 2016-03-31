<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Lib\Service\Map\Request;

use Praxigento\Downline\Lib\Service\Map\Request\Base as BaseRequest;

/**
 * Array with CustomerId & Depth attributes in items is expected as data to map.
 *
 * @method string getAsCustomerId()
 * @method void setAsCustomerId(string $data)
 * @method string getAsDepth()
 * @method void setAsDepth(string $data)
 * @method bool getShouldReversed()
 * @method void setShouldReversed(bool $data)
 */
class TreeByDepth extends BaseRequest {

}