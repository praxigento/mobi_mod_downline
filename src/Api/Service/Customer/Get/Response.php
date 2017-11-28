<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Get;

class Response
    extends \Praxigento\Core\Api\Service\Customer\Get\Response
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