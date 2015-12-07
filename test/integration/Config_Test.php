<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline;
include_once(__DIR__ . '/phpunit_bootstrap.php');

class Config_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_lib() {
        $ctx = \Praxigento\Downline\Lib\Context::instance();
        $this->assertTrue($ctx instanceof \Praxigento\Core\Lib\Context);
    }

}