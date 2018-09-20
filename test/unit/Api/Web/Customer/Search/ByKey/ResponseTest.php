<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Downline\Api\Web\Customer\Search\ByKey;

use Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response as AnObject;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class ResponseTest
    extends \Praxigento\Core\Test\BaseCase\Unit
{
    private function getItems()
    {
        $result = new \Praxigento\Downline\Api\Service\Customer\Search\Response\Item();
        $result->setCountry('LV');
        $result->setEmail('email');
        $result->setId(2);
        $result->setMlmId('mlm id');
        $result->setNameFirst('first');
        $result->setNameLast('last');
        $result->setPathFull('path');
        return [$result];
    }

    public function test_convert()
    {
        /* create object & convert it to 'JSON'-array */
        $obj = new AnObject();

        $items = $this->getItems();
        $data = new \Praxigento\Downline\Api\Service\Customer\Search\Response();
        $data->setItems($items);
        $obj->setData($data);

        /** @var \Magento\Framework\Webapi\ServiceOutputProcessor $output */
        $output = $this->manObj->get(\Magento\Framework\Webapi\ServiceOutputProcessor::class);
        $json = $output->convertValue($obj, AnObject::class);

        /* convert 'JSON'-array to object */
        /** @var \Magento\Framework\Webapi\ServiceInputProcessor $input */
        $input = $this->manObj->get(\Magento\Framework\Webapi\ServiceInputProcessor::class);
        $data = $input->convertValue($json, AnObject::class);
        $this->assertNotNull($data);
    }

}