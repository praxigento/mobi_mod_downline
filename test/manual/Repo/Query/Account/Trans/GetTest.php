<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Downline\Repo\Query\Account\Trans;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class GetTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_build()
    {
        /** @var \Praxigento\Downline\Repo\Query\Account\Trans\Get $obj */
        $obj = $this->manObj->create(\Praxigento\Downline\Repo\Query\Account\Trans\Get::class);
        $res = $obj->build();
        $this->assertNotNull($res);
    }

}