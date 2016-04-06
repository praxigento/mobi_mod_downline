<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Lib\Service\Customer\Request;


class ChangeParent extends \Praxigento\Core\Service\Base\Request {
    /**
     * Magento ID for customer itself.
     * @var int
     */
    const CUSTOMER_ID = 'customer_id';
    /**
     * UTC date.
     * @var string '2015-11-23 12:23:34'
     */
    const DATE = 'date';
    /**
     * Magento ID for customer's new parent.
     * @var int
     */
    const PARENT_ID_NEW = 'parent_id_new';
}