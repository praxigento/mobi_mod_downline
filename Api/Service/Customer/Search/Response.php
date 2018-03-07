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
     * @return \Praxigento\Downline\Api\Service\Customer\Search\Response\Item[]
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * @param \Praxigento\Downline\Api\Service\Customer\Search\Response\Item[] $data
     */
    public function setItems($data)
    {
        parent::setItems($data);
    }


}