<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap\Sub;


use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Snap;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class CalcSimple_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mToolPeriod;
    /** @var  CalcSimple */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mToolPeriod = $this->_mock(\Praxigento\Core\Tool\IPeriod::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new CalcSimple(
            $this->mToolPeriod
        );
    }

    public function test_calcSnapshots()
    {
        /** === Test Data === */
        $CURRENT_STATE = [
            1 => [
                Snap::ATTR_CUSTOMER_ID => 1,
                Snap::ATTR_PARENT_ID => 1,
                Snap::ATTR_PATH => '/',
                Snap::ATTR_DEPTH => 0
            ],
            2 => [
                Snap::ATTR_CUSTOMER_ID => 2,
                Snap::ATTR_PARENT_ID => 1,
                Snap::ATTR_PATH => '/1/',
                Snap::ATTR_DEPTH => 1
            ],
            3 => [
                Snap::ATTR_CUSTOMER_ID => 3,
                Snap::ATTR_PARENT_ID => 1,
                Snap::ATTR_PATH => '/1/',
                Snap::ATTR_DEPTH => 1
            ]
        ];
        $CHANGES = [
            /* root node customer, not in current state */
            [
                Change::ATTR_CUSTOMER_ID => 99,
                Change::ATTR_PARENT_ID => 99,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:00:00'
            ],
            /* not root node customer, not in current state */
            [
                Change::ATTR_CUSTOMER_ID => 4,
                Change::ATTR_PARENT_ID => 2,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:01:00'
            ],
            /* not root node customer, is in current state */
            [
                Change::ATTR_CUSTOMER_ID => 3,
                Change::ATTR_PARENT_ID => 2,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:02:00'
            ],
            /* move not root node to root node */
            [
                Change::ATTR_CUSTOMER_ID => 3,
                Change::ATTR_PARENT_ID => 3,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:03:00'
            ],
            /* move old root node with children to new root node */
            [
                Change::ATTR_CUSTOMER_ID => 1,
                Change::ATTR_PARENT_ID => 3,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:04:00'
            ],
        ];
        $PERIOD = '20151207';
        /** === Setup Mocks === */
        $this->mToolPeriod
            ->shouldReceive('getPeriodCurrent')->once()
            ->andReturn($PERIOD);
        /** === Call and asserts  === */
        $res = $this->obj->calcSnapshots($CURRENT_STATE, $CHANGES);
        $this->assertTrue(is_array($res));
    }

}