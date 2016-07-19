<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoChange;
    /** @var  \Mockery\MockInterface */
    private $mRepoSnap;
    /** @var  \Mockery\MockInterface */
    private $mSubCalc;
    /** @var  \Mockery\MockInterface */
    private $mToolPeriod;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mToolPeriod = $this->_mock(\Praxigento\Core\Tool\IPeriod::class);
        $this->mRepoChange = $this->_mock(\Praxigento\Downline\Repo\Entity\IChange::class);
        $this->mRepoSnap = $this->_mock(\Praxigento\Downline\Repo\Entity\ISnap::class);
        $this->mSubCalc = $this->_mock(Sub\CalcSimple::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManTrans,
            $this->mToolPeriod,
            $this->mRepoChange,
            $this->mRepoSnap,
            $this->mSubCalc
        );
    }

    public function test_calc()
    {
        /** === Test Data === */
        $DS_TO = '20151207';
        $DS_LAST = 'the last datestamp';
        $SNAPSHOT = 'snapshot';
        $TS_FROM = 'from';
        $TS_TO = 'to';
        $CHANGE_LOG = 'change log';
        $UPDATES = 'updates';
        /** === Setup Mocks === */
        $this->obj = \Mockery::mock(
            Call::class . '[getLastDate]',
            [
                $this->mLogger,
                $this->mManTrans,
                $this->mToolPeriod,
                $this->mRepoChange,
                $this->mRepoSnap,
                $this->mSubCalc
            ]
        );
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $respLast = $this->getLastDate($reqLast);
        $mRespLast = new \Praxigento\Downline\Service\Snap\Response\GetLastDate();
        $this->obj
            ->shouldReceive('getLastDate')->once()
            ->andReturn($mRespLast);
        // $lastDatestamp = $respLast->getLastDate();
        $mRespLast->setLastDate($DS_LAST);
        // $snapshot = $this->_repoSnap->getStateOnDate($lastDatestamp);
        $this->mRepoSnap
            ->shouldReceive('getStateOnDate')->once()
            ->andReturn($SNAPSHOT);
        // $tsFrom = $this->_toolPeriod->getTimestampNextFrom($lastDatestamp);
        $this->mToolPeriod
            ->shouldReceive('getTimestampNextFrom')->once()
            ->andReturn($TS_FROM);
        // $tsTo = $this->_toolPeriod->getTimestampTo($periodTo);
        $this->mToolPeriod
            ->shouldReceive('getTimestampTo')->once()
            ->andReturn($TS_TO);
        // $changelog = $this->_repoChange->getChangesForPeriod($tsFrom, $tsTo);
        $this->mRepoChange
            ->shouldReceive('getChangesForPeriod')->once()
            ->andReturn($CHANGE_LOG);
        // $updates = $this->_subCalc->calcSnapshots($snapshot, $changelog);
        $this->mSubCalc
            ->shouldReceive('calcSnapshots')->once()
            ->andReturn($UPDATES);
        // $this->_repoSnap->saveCalculatedUpdates($updates);
        $this->mRepoSnap
            ->shouldReceive('saveCalculatedUpdates')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Calc();
        $req->setDatestampTo($DS_TO);
        $resp = $this->obj->calc($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Call::class, $this->obj);
    }

    public function test_expandMinimal()
    {
        /** === Test Data === */
        $TREE = [
            2 => 1,
            3 => 1,
            4 => 2,
            5 => 2,
            6 => 3,
            7 => 3,
            20 => 20,
            10 => 7,
            11 => 7,
            1 => 1,
            12 => 10,
            12 => 121
        ];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $req = new Request\ExpandMinimal();
        $req->setTree($TREE);
        $resp = $this->obj->expandMinimal($req);
        $this->assertTrue($resp->isSucceed());
        $snapData = $resp->getSnapData();
        $this->assertTrue(is_array($snapData));
    }

    public function test_getLastDate_isSnapshot()
    {
        /** === Test Data === */
        $DS_SNAP_MAX = '20151206';
        /** === Setup Mocks === */
        // $snapMaxDate = $this->_repoSnap->getMaxDatestamp();
        $this->mRepoSnap
            ->shouldReceive('getMaxDatestamp')->once()
            ->andReturn($DS_SNAP_MAX);
        /** === Call and asserts  === */
        $req = new Request\GetLastDate();
        $resp = $this->obj->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($DS_SNAP_MAX, $resp->getLastDate());
    }

    public function test_getLastDate_noSnapshot()
    {
        /** === Test Data === */
        $TS_MIN_DATE = '2015-12-07 10:00:00';
        $PERIOD = 'period';
        $DS_DAY_BEFORE = '20151206';
        /** === Setup Mocks === */
        // $snapMaxDate = $this->_repoSnap->getMaxDatestamp();
        $this->mRepoSnap
            ->shouldReceive('getMaxDatestamp')->once()
            ->andReturn(null);
        // $changelogMinDate = $this->_repoChange->getChangelogMinDate();
        $this->mRepoChange
            ->shouldReceive('getChangelogMinDate')->once()
            ->andReturn($TS_MIN_DATE);
        // $period = $this->_toolPeriod->getPeriodCurrent($changelogMinDate);
        $this->mToolPeriod
            ->shouldReceive('getPeriodCurrent')->once()
            ->andReturn($PERIOD);
        // $dayBefore = $this->_toolPeriod->getPeriodPrev($period);
        $this->mToolPeriod
            ->shouldReceive('getPeriodPrev')->once()
            ->andReturn($DS_DAY_BEFORE);
        /** === Call and asserts  === */
        $req = new Request\GetLastDate();
        $resp = $this->obj->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($DS_DAY_BEFORE, $resp->getLastDate());
    }

    public function test_getStateOnDate()
    {
        /** === Test Data === */
        $DS = '20151206';
        $ROWS = 'rows';
        /** === Setup Mocks === */
        // $rows = $this->_repoSnap->getStateOnDate($dateOn);
        $this->mRepoSnap
            ->shouldReceive('getStateOnDate')->once()
            ->andReturn($ROWS);
        /** === Call and asserts  === */
        $req = new Request\GetStateOnDate();
        $req->setDatestamp($DS);
        $resp = $this->obj->getStateOnDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($ROWS, $resp->getData());
    }
}