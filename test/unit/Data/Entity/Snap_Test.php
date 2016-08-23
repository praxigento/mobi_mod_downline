<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Snap_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  Snap */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Snap();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $CUSTOMER_ID = 'cust id';
        $DATE = 'date';
        $DEPTH = 'depth';
        $PARENT_ID = 'parent id';
        $PATH = 'path';
        /** === Call and asserts  === */
        $this->obj->setCustomerId($CUSTOMER_ID);
        $this->obj->setDate($DATE);
        $this->obj->setDepth($DEPTH);
        $this->obj->setParentId($PARENT_ID);
        $this->obj->setPath($PATH);
        $this->assertEquals($CUSTOMER_ID, $this->obj->getCustomerId());
        $this->assertEquals($DATE, $this->obj->getDate());
        $this->assertEquals($DEPTH, $this->obj->getDepth());
        $this->assertEquals($PARENT_ID, $this->obj->getParentId());
        $this->assertEquals($PATH, $this->obj->getPath());
    }
}