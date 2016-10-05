<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg\Def\Account;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

use Praxigento\Downline\Data\Agg\Account as Agg;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Mapper_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Agg\Mapper
{
    /** @var  Mapper */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Mapper();
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Mapper::class, $this->obj);
    }

    public function test_get()
    {
        /** === Call and asserts  === */
        $res = $this->obj->get(Agg::AS_REF);
        $this->assertEquals('pdc.human_ref', $res);
    }
}