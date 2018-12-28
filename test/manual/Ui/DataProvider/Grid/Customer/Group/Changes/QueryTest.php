<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Downline\Ui\DataProvider\Grid\Customer\Group\Changes;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class QueryTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_exec()
    {
        /** @var  $obj \Praxigento\Downline\Ui\DataProvider\Grid\Customer\Group\Changes\Query */
        $obj = $this->manObj->get(\Praxigento\Downline\Ui\DataProvider\Grid\Customer\Group\Changes\Query::class);
        /** @var \Magento\Framework\Api\Search\SearchCriteriaInterface $search */
        $search = $this->manObj->get(\Magento\Framework\Api\Search\SearchCriteriaInterface::class);
        $resp = $obj->getItems($search);
        $this->assertTrue(is_array($resp));
    }
}