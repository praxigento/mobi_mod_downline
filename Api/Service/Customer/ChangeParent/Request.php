<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\ChangeParent;

/**
 * @method int getCustomerId()
 * @method void setCustomerId(int $data)
 * @method string getDate()
 * @method void setDate(string $data)
 * @method int getNewParentId()
 * @method void setNewParentId(int $data)
 */
class Request
    extends \Praxigento\Core\App\Service\Request
{
    const CUSTOMER_ID = 'customerId';
    const DATE = 'date';
    const NEW_PARENT_ID = 'newParentId';

}