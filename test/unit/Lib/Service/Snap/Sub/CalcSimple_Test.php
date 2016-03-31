<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap\Sub;


use Praxigento\Downline\Lib\Entity\Change;
use Praxigento\Downline\Lib\Entity\Snap;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class CalcSimple_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_calcSnapshots() {
        /** === Test Data === */
        $CURRENT_STATE = [
            1 => [
                Snap::ATTR_CUSTOMER_ID => 1,
                Snap::ATTR_PARENT_ID   => 1,
                Snap::ATTR_PATH        => '/',
                Snap::ATTR_DEPTH       => 0
            ],
            2 => [
                Snap::ATTR_CUSTOMER_ID => 2,
                Snap::ATTR_PARENT_ID   => 1,
                Snap::ATTR_PATH        => '/1/',
                Snap::ATTR_DEPTH       => 1
            ],
            3 => [
                Snap::ATTR_CUSTOMER_ID => 3,
                Snap::ATTR_PARENT_ID   => 1,
                Snap::ATTR_PATH        => '/1/',
                Snap::ATTR_DEPTH       => 1
            ]
        ];
        $CHANGES = [
            /* root node customer, not in current state */
            [
                Change::ATTR_CUSTOMER_ID  => 99,
                Change::ATTR_PARENT_ID    => 99,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:00:00'
            ],
            /* not root node customer, not in current state */
            [
                Change::ATTR_CUSTOMER_ID  => 4,
                Change::ATTR_PARENT_ID    => 2,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:01:00'
            ],
            /* not root node customer, is in current state */
            [
                Change::ATTR_CUSTOMER_ID  => 3,
                Change::ATTR_PARENT_ID    => 2,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:02:00'
            ],
            /* move not root node to root node */
            [
                Change::ATTR_CUSTOMER_ID  => 3,
                Change::ATTR_PARENT_ID    => 3,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:03:00'
            ],
            /* move old root node with children to new root node */
            [
                Change::ATTR_CUSTOMER_ID  => 1,
                Change::ATTR_PARENT_ID    => 3,
                Change::ATTR_DATE_CHANGED => '2015-12-07 12:04:00'
            ],
        ];
        /** === Mocks === */
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Lib\Tool\Period');
        // $dsChanged = $toolPeriod->getPeriodCurrent($tsChanged);
        $mToolPeriod
            ->expects($this->any())
            ->method('getPeriodCurrent')
            ->willReturn('20151207');

        /**
         * Prepare request and perform call.
         */
        $mToolbox = $this->_mockToolbox(null, null, null, $mToolPeriod);
        /** === Test itself === */
        /** @var  $sub CalcSimple */
        $sub = new CalcSimple($mToolbox);
        $res = $sub->calcSnapshots($CURRENT_STATE, $CHANGES);

    }

}