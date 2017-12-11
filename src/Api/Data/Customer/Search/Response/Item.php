<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Data\Customer\Search\Response;

/**
 * Extended result set item with found customers data.
 */
class Item
    extends \Praxigento\Core\Api\Data\Customer\Search\Response\Item
{
    const MLM_ID = 'mlmId';

    /**
     * @return string
     */
    public function getMlmId() {
        $result = parent::get(self::MLM_ID);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setMlmId($data) {
        parent::set(self::MLM_ID, $data);
    }
}