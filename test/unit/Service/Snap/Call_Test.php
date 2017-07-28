<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Call_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Service\Call
{
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
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];


    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mToolPeriod = $this->_mock(\Praxigento\Core\Tool\IPeriod::class);
        $this->mRepoChange = $this->_mock(\Praxigento\Downline\Repo\Entity\Def\Change::class);
        $this->mRepoSnap = $this->_mock(\Praxigento\Downline\Repo\Entity\Def\Snap::class);
        $this->mSubCalc = $this->_mock(Sub\CalcSimple::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mLogger,
            $this->mManObj,
            $this->mManTrans,
            $this->mToolPeriod,
            $this->mRepoChange,
            $this->mRepoSnap,
            $this->mSubCalc
        ];
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj,
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
        $dsTo = '20151207';
        $dsLast = 'the last datestamp';
        $snapshot = 'snapshot';
        $tsFrom = 'from';
        $tsTo = 'to';
        $changeLog = 'change log';
        $updates = 'updates';
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Call::class . '[getLastDate]', $this->objArgs);
        /** === Setup Mocks === */
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
        $mRespLast->setLastDate($dsLast);
        // $snapshot = $this->_repoSnap->getStateOnDate($lastDatestamp);
        $this->mRepoSnap
            ->shouldReceive('getStateOnDate')->once()
            ->andReturn($snapshot);
        // $tsFrom = $this->_toolPeriod->getTimestampNextFrom($lastDatestamp);
        $this->mToolPeriod
            ->shouldReceive('getTimestampNextFrom')->once()
            ->andReturn($tsFrom);
        // $tsTo = $this->_toolPeriod->getTimestampTo($periodTo);
        $this->mToolPeriod
            ->shouldReceive('getTimestampTo')->once()
            ->andReturn($tsTo);
        // $changelog = $this->_repoChange->getChangesForPeriod($tsFrom, $tsTo);
        $this->mRepoChange
            ->shouldReceive('getChangesForPeriod')->once()
            ->andReturn($changeLog);
        // $updates = $this->_subCalc->calcSnapshots($snapshot, $changelog);
        $this->mSubCalc
            ->shouldReceive('calcSnapshots')->once()
            ->andReturn($updates);
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
        $req->setDatestampTo($dsTo);
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
        $tree = [
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
        $req->setTree($tree);
        $resp = $this->obj->expandMinimal($req);
        $this->assertTrue($resp->isSucceed());
        $snapData = $resp->getSnapData();
        $this->assertTrue(is_array($snapData));
    }

    public function test_getLastDate_isSnapshot()
    {
        /** === Test Data === */
        $dsSnapMax = '20151206';
        /** === Setup Mocks === */
        // $snapMaxDate = $this->_repoSnap->getMaxDatestamp();
        $this->mRepoSnap
            ->shouldReceive('getMaxDatestamp')->once()
            ->andReturn($dsSnapMax);
        /** === Call and asserts  === */
        $req = new Request\GetLastDate();
        $resp = $this->obj->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($dsSnapMax, $resp->getLastDate());
    }

    public function test_getLastDate_noSnapshot()
    {
        /** === Test Data === */
        $tsMinDate = '2015-12-07 10:00:00';
        $period = 'period';
        $dsDayBefore = '20151206';
        /** === Setup Mocks === */
        // $snapMaxDate = $this->_repoSnap->getMaxDatestamp();
        $this->mRepoSnap
            ->shouldReceive('getMaxDatestamp')->once()
            ->andReturn(null);
        // $changelogMinDate = $this->_repoChange->getChangelogMinDate();
        $this->mRepoChange
            ->shouldReceive('getChangelogMinDate')->once()
            ->andReturn($tsMinDate);
        // $period = $this->_toolPeriod->getPeriodCurrentOld($changelogMinDate);
        $this->mToolPeriod
            ->shouldReceive('getPeriodCurrentOld')->once()
            ->andReturn($period);
        // $dayBefore = $this->_toolPeriod->getPeriodPrev($period);
        $this->mToolPeriod
            ->shouldReceive('getPeriodPrev')->once()
            ->andReturn($dsDayBefore);
        /** === Call and asserts  === */
        $req = new Request\GetLastDate();
        $resp = $this->obj->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($dsDayBefore, $resp->getLastDate());
    }

    public function test_getStateOnDate()
    {
        /** === Test Data === */
        $dstamp = '20151206';
        $rows = 'rows';
        /** === Setup Mocks === */
        // $rows = $this->_repoSnap->getStateOnDate($dateOn);
        $this->mRepoSnap
            ->shouldReceive('getStateOnDate')->once()
            ->andReturn($rows);
        /** === Call and asserts  === */
        $req = new Request\GetStateOnDate();
        $req->setDatestamp($dstamp);
        $resp = $this->obj->getStateOnDate($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($rows, $resp->get());
    }
}