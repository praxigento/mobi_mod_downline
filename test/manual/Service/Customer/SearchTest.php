<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Manual\Praxigento\Downline\Service\Customer;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class SearchTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_exec()
    {
        /** @var \Praxigento\Core\Api\Service\Customer\Search $obj */
        $obj = $this->manObj->get(\Praxigento\Core\Api\Service\Customer\Search::class);
        $req = new \Praxigento\Downline\Api\Service\Customer\Search\Request();
        $req->setSearchKey('buk');
        $res = $obj->exec($req);
        $this->assertNotNull($res->getData());
    }

}