<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Change_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Change */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Change();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $CUST_ID = 'cust ID';
        $DATE_CHANGED = 'changed at';
        $ID = 'id';
        $PARENT_ID = 'id';
        /** === Call and asserts  === */
        $this->obj->setCustomerId($CUST_ID);
        $this->obj->setDateChanged($DATE_CHANGED);
        $this->obj->setId($ID);
        $this->obj->setParentId($PARENT_ID);
        $this->assertEquals($CUST_ID, $this->obj->getCustomerId());
        $this->assertEquals($DATE_CHANGED, $this->obj->getDateChanged());
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($PARENT_ID, $this->obj->getParentId());
    }
}