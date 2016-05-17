<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Praxigento\Downline\Data\Entity\Snap as Entity;
use Praxigento\Downline\Repo\Entity\ISnap;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Snap_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  \Mockery\MockInterface */
    private $mRsrc;
    /** @var  Snap */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /** setup mocks for constructor */
        $this->mRsrc = $this->_mockResourceConnection($this->mConn);
        /** create object to test */
        $this->obj = new Snap(
            $this->mRsrc,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(ISnap::class, $this->obj);
    }

    public function test_getMaxDatestamp()
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
        $res = $this->obj->getMaxDatestamp();
        $this->assertEquals($RESULT, $res);
    }

    public function test_getStateOnDate()
    {
        /** === Test Data === */
        $DS = 'datestamp';
        $TBL_SNAP = 'snap';
        $CUST_ID = 32;
        $ROW = [Entity::ATTR_CUSTOMER_ID => $CUST_ID];
        $ROWS = [$ROW];
        /** === Setup Mocks === */
        // $tblSnap = $this->_conn->getTableName(Snap::ENTITY_NAME);
        $this->mConn
            ->shouldReceive('getTableName')->once()
            ->andReturn($TBL_SNAP);
        // $q4Max = $this->_conn->select();
        $mQ4Max = $this->_mockDbSelect();
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mQ4Max);
        $mQ4Max->shouldReceive('from', 'where', 'order', 'group', 'joinLeft');
        // $rows = $this->_conn->fetchAll($query, $bind);
        $this->mConn
            ->shouldReceive('fetchAll')->once()
            ->andReturn($ROWS);
        /** === Call and asserts  === */
        $res = $this->obj->getStateOnDate($DS);
        $this->assertEquals($ROW, $res[$CUST_ID]);
    }

    public function test_saveCalculatedUpdates()
    {
        /** === Test Data === */
        $UPDATES = [
            'date' => [
                ['update1']
            ]
        ];
        $this->obj = \Mockery::mock(
            Snap::class . '[create]',
            [$this->mRsrc, $this->mRepoGeneric, Entity::class]);
        /** === Setup Mocks === */
        // $this->create($data);
        $this->obj
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $this->obj->saveCalculatedUpdates($UPDATES);
    }
}