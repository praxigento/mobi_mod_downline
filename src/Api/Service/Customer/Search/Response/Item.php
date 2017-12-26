<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Search\Response;

/**
 * Modules's shell extends underline core service data object directly.
 *
 * (Define getters explicitly to use with Swagger tool)
 */
class Item
    extends \Praxigento\Core\Api\Service\Customer\Search\Response\Item
{
    const MLM_ID = 'mlm_id';

    /**
     * @return string
     */
    public function getMlmId()
    {
        $result = parent::get(self::MLM_ID);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setMlmId($data)
    {
        parent::set(self::MLM_ID, $data);
    }
}