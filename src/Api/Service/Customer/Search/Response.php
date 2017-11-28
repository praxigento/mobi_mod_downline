<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Search;

/**
 * Modules's shell extends underline core service response directly.
 */
class Response
    extends \Praxigento\Core\Api\Service\Customer\Search\Response
{
    /**
     * @return \Praxigento\Downline\Api\Service\Customer\Search\Response\Data
     */
    public function getData()
    {
        return parent::getData();
    }

    /**
     * @param \Praxigento\Downline\Api\Service\Customer\Search\Response\Data $data
     */
    public function setData($data)
    {
        parent::setData($data);
    }

}