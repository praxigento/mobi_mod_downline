<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Customer\Get\ById\Request;

/**
 * Add MLM ID to request parameters.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Data
    extends \Praxigento\Core\Api\Web\Customer\Get\ById\Request\Data
{
    const MLM_ID = 'mlmId';

    /**
     * @return string|null
     */
    public function getMlmId() {
        $result = parent::get(self::MLM_ID);
        return $result;
    }

    /**
     * @param string $data
     * @return null
     */
    public function setMlmId($data) {
        parent::set(self::MLM_ID, $data);
    }
}