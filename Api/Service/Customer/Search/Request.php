<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Search;

/**
 * Modules's shell extends underline core service request directly.
 */
class Request
    extends \Praxigento\Core\Api\Service\Customer\Search\Request
{
    /** Root customer id for the downline tree. */
    const CUSTOMER_ID = 'customerId';

    /** @return int */
    public function getCustomerId()
    {
        $result = parent::get(self::CUSTOMER_ID);
        return $result;
    }

    /**
     * @param int $data
     * @return void
     */
    public function setCustomerId($data)
    {
        parent::set(self::CUSTOMER_ID, $data);
    }
}