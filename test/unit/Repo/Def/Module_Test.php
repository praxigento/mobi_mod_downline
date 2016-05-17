<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Def;

use Praxigento\Downline\Repo\IModule;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Module_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  Module */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $mRsrc = $this->_mockResourceConnection($this->mConn);
        $this->obj = new Module(
            $mRsrc
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IModule::class, $this->obj);
    }

    public function test_getChangelogMinDate()
    {
        /** === Test Data === */
        $TABLE = 'tbl';
        $RESULT = 'result';
        /** === Setup Mocks === */
        //         $tblChange = $this->_conn->getTableName(Change::ENTITY_NAME);
        $this->mConn
            ->shouldReceive('getTableName')->once()
            ->andReturn($TABLE);
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mQuery);
        // $query->from([$asChange => $tblChange], [Change::ATTR_DATE_CHANGED]);
        // $query->order([$asChange . '.' . Change::ATTR_DATE_CHANGED . ' ASC']);
        $mQuery->shouldReceive('from', 'order');
        // $result = $this->_conn->fetchOne($query);
        $this->mConn
            ->shouldReceive('fetchOne')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getChangelogMinDate();
        $this->assertEquals($RESULT, $res);
    }

    public function test_getChangesForPeriod()
    {
        /** === Test Data === */
        $FROM = 'from';
        $TO = 'to';
        $TABLE = 'table';
        $RESULT = 'result';
        /** === Setup Mocks === */
        // $tblChange = $this->_conn->getTableName(Change::ENTITY_NAME);
        $this->mConn
            ->shouldReceive('getTableName')->once()
            ->andReturn($TABLE);
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mQuery);
        $mQuery->shouldReceive('from', 'where', 'order');
        // $result = $this->_conn->fetchAll($query, $bind);
        $this->mConn
            ->shouldReceive('fetchAll')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getChangesForPeriod($FROM, $TO);
        $this->assertEquals($RESULT, $res);
    }

    public function test_getSnapMaxDatestamp()
    {
        /** === Test Data === */
        $TABLE = 'table';
        $RESULT = 'result';
        /** === Setup Mocks === */
        // $tblSnap = $this->_conn->getTableName(Snap::ENTITY_NAME);
        $this->mConn
            ->shouldReceive('getTableName')->once()
            ->andReturn($TABLE);
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mQuery);
        $mQuery->shouldReceive('from', 'order');
        // $result = $this->_conn->fetchOne($query);
        $this->mConn
            ->shouldReceive('fetchOne')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getSnapMaxDatestamp();
        $this->assertEquals($RESULT, $res);
    }

}