<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Controller\Asset\Transfer;

class Init
    extends \Praxigento\Core\App\Action\Front\Base
{
    protected function getInDataType(): string
    {
        return \Praxigento\Downline\Api\Customer\Search\Request::class;
    }

    protected function getOutDataType(): string
    {
        return \Praxigento\Downline\Api\Customer\Search\Response::class;
    }

    protected function process($data)
    {
        $result = new \Praxigento\Downline\Api\Customer\Search\Response();
        $data = new \Praxigento\Downline\Api\Customer\Search\Response\Data();
        $item = new \Praxigento\Downline\Api\Customer\Search\Response\Data\Item();
        $item->setEmail('email');
        $items = [$item];
        $data->setItems($items);
        $result->setData($data);
        return $result;
    }


}