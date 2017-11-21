<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Manual\Praxigento\Downline\Service\Customer;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Search_Test
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_exec()
    {
        /** @var \Praxigento\Downline\Api\Customer\SearchInterface $obj */
        $obj = $this->manObj->get(\Praxigento\Downline\Api\Customer\SearchInterface::class);
        $req = new \Praxigento\Downline\Api\Customer\Search\Request();
        $req->setSearchKey('buk');
        $res = $obj->exec($req);
        $this->assertNotNull($res->getData());
    }

}