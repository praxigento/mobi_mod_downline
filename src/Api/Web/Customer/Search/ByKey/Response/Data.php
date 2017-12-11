<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response;

/**
 * Contains suggestions for customers found by key (name/email/mlm_id).
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Data
    extends \Praxigento\Core\Api\Web\Customer\Search\ByKey\Response\Data
{

    /**
     * @return \Praxigento\Downline\Api\Data\Customer\Search\Response\Item[]
     */
    public function getItems() {
        $result = parent::get(self::ITEMS);
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Data\Customer\Search\Response\Item[] $data
     */
    public function setItems($data) {
        parent::set(self::ITEMS, $data);
    }
}