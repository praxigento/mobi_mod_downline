<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Helper;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Config_ManualTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_getReferralsRootAnonymous()
    {
        /** === Test Data === */
        /** === Call and asserts  === */
        /** @var Config $obj */
        $obj = $this->manObj->create(Config::class);
        $res = $obj->getReferralsRootAnonymous();
        $this->assertFalse($res);
    }
}