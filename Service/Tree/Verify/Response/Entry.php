<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Service\Tree\Verify\Response;

/**
 * @method string getActPath()
 * @method void setActPath(string $data)
 * @method int getCustomerId()
 * @method void setCustomerId(int $data)
 * @method string getExpPath()
 * @method void setExpPath(string $data)
 * @method int getParentId()
 * @method void setParentId(int $data)
 */
class Entry
    extends \Praxigento\Core\Data
{
    const ACT_PATH = 'actPath';
    const CUSTOMER_ID = 'customerId';
    const EXP_PATH = 'expPath';
    const PARENT_ID = 'parentId';
}