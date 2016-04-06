<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_calc_withGetLastDate() {
        /** === Test Data === */
        $DS_TO = '20151207';
        $DS_MAX = '20151206';
        $SNAPSHOT = [ ];
        $CHANGELOG = [ ];
        $UPDATES = [ ];
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Tool\IPeriod');
        $mToolbox = $this->_mockToolbox(null, null, null, $mToolPeriod);
        $mCallRepo = $this->_mockCallRepo();
        $mSubDb = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\Db');
        $mSubCalc = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\CalcSimple');

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $snapMaxDate = $this->_subDb->getSnapMaxDatestamp(); (from getLastDate)
        $mSubDb
            ->expects($this->any())
            ->method('getSnapMaxDatestamp')
            ->willReturn($DS_MAX);
        // $snapshot = $this->_subDb->getStateOnDate($lastDatestamp);
        $mSubDb
            ->expects($this->once())
            ->method('getStateOnDate')
            ->with($this->equalTo($DS_MAX))
            ->willReturn($SNAPSHOT);
        // $changelog = $this->_subDb->getChangesForPeriod($tsFrom, $tsTo);
        $mSubDb
            ->expects($this->once())
            ->method('getChangesForPeriod')
            ->willReturn($CHANGELOG);
        // $updates = $this->_subCalc->calcSnapshots($snapshot, $changelog);
        $mSubCalc
            ->expects($this->once())
            ->method('calcSnapshots')
            ->willReturn($UPDATES);
        // $this->_subDb->saveCalculatedUpdates($updates);
        $mSubDb
            ->expects($this->once())
            ->method('saveCalculatedUpdates');
        // $this->_conn->commit();
        $mConn
            ->expects($this->once())
            ->method('commit');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mSubDb, $mSubCalc);
        $req = new Request\Calc();
        $req->setDatestampTo($DS_TO);
        $resp = $call->calc($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_calc_exception() {
        /** === Test Data === */
        $DS_TO = '20151207';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Tool\IPeriod');
        $mToolbox = $this->_mockToolbox(null, null, null, $mToolPeriod);
        $mCallRepo = $this->_mockCallRepo();
        $mSubDb = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\Db');
        $mSubCalc = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\CalcSimple');

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction')
            ->willThrowException(new \Exception());
        // $this->_conn->rollback();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mSubDb, $mSubCalc);
        $req = new Request\Calc();
        $req->setDatestampTo($DS_TO);
        $resp = $call->calc($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_extendMinimal() {
        /** === Test Data === */
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Tool\IPeriod');
        $mToolbox = $this->_mockToolbox(null, null, null, $mToolPeriod);
        $mCallRepo = $this->_mockCallRepo();
        $mSubDb = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\Db');
        $mSubCalc = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\CalcSimple');

        //
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mSubDb, $mSubCalc);
        $req = new Request\ExpandMinimal();
        $req->setTree([
            2  => 1,
            3  => 1,
            4  => 2,
            5  => 2,
            6  => 3,
            7  => 3,
            20 => 20,
            10 => 7,
            11 => 7,
            1  => 1,
            12 => 10,
            12 => 121
        ]);
        $resp = $call->expandMinimal($req);
        $this->assertTrue($resp->isSucceed());
        $snapData = $resp->getSnapData();
        $this->assertTrue(is_array($snapData));
    }

    public function test_getLastDate_noSnapshot() {
        /** === Test Data === */
        $TS_MIN_DATE = '2015-12-07 10:00:00';
        $DS_CURRENT = '20151207';
        $DS_PREV = '20151206';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Tool\IPeriod');
        $mToolbox = $this->_mockToolbox(null, null, null, $mToolPeriod);
        $mCallRepo = $this->_mockCallRepo();
        $mSubDb = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\Db');
        $mSubCalc = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\CalcSimple');

        // $snapMaxDate = $this->_subDb->getSnapMaxDatestamp();
        $mSubDb
            ->expects($this->once())
            ->method('getSnapMaxDatestamp')
            ->willReturn(null);
        // $changelogMinDate = $this->_subDb->getChangelogMinDate();
        $mSubDb
            ->expects($this->once())
            ->method('getChangelogMinDate')
            ->willReturn($TS_MIN_DATE);
        // $period = $toolPeriod->getPeriodCurrent($changelogMinDate);
        $mToolPeriod
            ->expects($this->once())
            ->method('getPeriodCurrent')
            ->with($this->equalTo($TS_MIN_DATE))
            ->willReturn($DS_CURRENT);
        // $dayBefore = $toolPeriod->getPeriodPrev($period);
        $mToolPeriod
            ->expects($this->once())
            ->method('getPeriodPrev')
            ->with($this->equalTo($DS_CURRENT))
            ->willReturn($DS_PREV);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mSubDb, $mSubCalc);
        $req = new Request\GetLastDate();
        $resp = $call->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($DS_PREV, $resp->getLastDate());
    }

    public function test_getStateOnDate() {
        /** === Test Data === */
        $DS = '20151206';
        $ROWS = [ ];
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Tool\IPeriod');
        $mToolbox = $this->_mockToolbox(null, null, null, $mToolPeriod);
        $mCallRepo = $this->_mockCallRepo();
        $mSubDb = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\Db');
        $mSubCalc = $this->_mockFor('Praxigento\Downline\Lib\Service\Snap\Sub\CalcSimple');

        // $rows = $this->_subDb->getStateOnDate($dateOn);
        $mSubDb
            ->expects($this->once())
            ->method('getStateOnDate')
            ->with($this->equalTo($DS))
            ->willReturn($ROWS);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mSubDb, $mSubCalc);
        $req = new Request\GetStateOnDate();
        $req->setDatestamp($DS);
        $resp = $call->getStateOnDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertTrue(is_array($resp->getData()));
    }
}