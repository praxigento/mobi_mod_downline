<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Downline\SwitchUp;

/**
 * @method int getCustomerId()
 * @method void setCustomerId(int $data)
 * @method string getDate()
 * @method void setDate(string $data)
 */
class Request
    extends \Praxigento\Core\App\Service\Request
{
    const CUSTOMER_ID = 'customerId';
    const DATE = 'date';
}