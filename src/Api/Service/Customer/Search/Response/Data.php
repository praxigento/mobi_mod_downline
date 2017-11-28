<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Search\Response;

/**
 * Modules's shell extends underline core service data object directly.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Data
    extends \Praxigento\Core\Api\Service\Customer\Search\Response\Data
{
    /**
     * @return \Praxigento\Downline\Api\Service\Customer\Search\Response\Data\Item[]
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * @param \Praxigento\Downline\Api\Service\Customer\Search\Response\Data\Item[] $data
     */
    public function setItems($data)
    {
        parent::setItems($data);
    }

}