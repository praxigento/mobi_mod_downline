<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Tool\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Tree_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {
    /** @var  Tree */
    private $obj;

    protected function setUp() {
        parent::setUp();
        $this->obj = new Tree();
    }

    public function test_expandMinimal_withoutKey() {
        /** === Test Data === */
        $TREE = [
            1 => 1,
            2 => 1,
            4 => 5,
        ];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $res = $this->obj->expandMinimal($TREE);
        $this->assertTrue(is_array($res));
        $this->assertEquals(3, count($res));
    }

    public function test_getParentsFromPathReversed() {
        /** === Test Data === */
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $res = $this->obj->getParentsFromPathReversed("/1/2/3/4/");
        $this->assertTrue(is_array($res));
        $this->assertEquals(4, count($res));
    }

}