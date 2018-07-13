<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Get\ById;

class Request
    extends \Praxigento\Core\Api\Service\Customer\Get\ById\Request
{
    const MLM_ID = 'mlmId';

    /**
     * @return string|null
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