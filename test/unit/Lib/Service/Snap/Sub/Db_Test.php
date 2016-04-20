<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap\Sub;

use Praxigento\Downline\Data\Entity\Snap;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Db_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Test is deprecated after M1 & M2 merge is done.');
    }

    public function test_getChangelogMinDate()
    {
        /** === Test Data === */
        $TABLE = 'table name';
        $RESULT = 'some result';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->willReturn($mQuery);
        // $result = $this->_conn->fetchOne($query);
        $mConn
            ->expects($this->once())
            ->method('fetchOne')
            ->willReturn($RESULT);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $sub Db */
        $sub = new Db($mLogger, $mDba, $mToolbox, $mCallRepo);
        $res = $sub->getChangelogMinDate();
        $this->assertEquals($RESULT, $res);
    }

    public function test_getChangesForPeriod()
    {
        /** === Test Data === */
        $TIMESTAMP_FROM = '2345/12/07 10:20:30';
        $TIMESTAMP_TO = '2345/12/08 10:20:30';
        $TABLE = 'table name';
        $RESULT = [];

        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->willReturn($mQuery);
        // $result = $this->_conn->fetchAll($query, $bind);
        $mConn
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn($RESULT);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $sub Db */
        $sub = new Db($mLogger, $mDba, $mToolbox, $mCallRepo);
        $res = $sub->getChangesForPeriod($TIMESTAMP_FROM, $TIMESTAMP_TO);
        $this->assertEquals($RESULT, $res);
    }

    public function test_getSnapMaxDatestamp()
    {
        /** === Test Data === */
        $TABLE = 'table name';
        $RESULT = 'result here';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->once())
            ->method('select')
            ->willReturn($mQuery);
        // $result = $this->_conn->fetchOne($query);
        $mConn
            ->expects($this->once())
            ->method('fetchOne')
            ->willReturn($RESULT);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $sub Db */
        $sub = new Db($mLogger, $mDba, $mToolbox, $mCallRepo);
        $res = $sub->getSnapMaxDatestamp();
        $this->assertEquals($RESULT, $res);
    }

    public function test_getStateOnDate()
    {
        /** === Test Data === */
        $DATESTAMP = '20161223';
        $TABLE = 'table name';
        $FETCHED = [
            [Snap::ATTR_CUSTOMER_ID => 21]
        ];
        $RESULT = [21 => [Snap::ATTR_CUSTOMER_ID => 21]];
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $q4Max = $this->_conn->select();
        $mQ4Max = $this->_mockDbSelect();
        $mConn
            ->expects($this->at(0))
            ->method('select')
            ->willReturn($mQ4Max);
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->at(1))
            ->method('select')
            ->willReturn($mQuery);
        // $rows = $this->_conn->fetchAll($query, $bind);
        $mConn
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn($FETCHED);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $sub Db */
        $sub = new Db($mLogger, $mDba, $mToolbox, $mCallRepo);
        $res = $sub->getStateOnDate($DATESTAMP);
        $this->assertEquals($RESULT, $res);
    }

    public function test_saveCalculatedUpdates()
    {
        /** === Test Data === */
        $TABLE = 'table name';
        $UPDATES = [
            '20151201' => [
                21 => [],
                32 => []
            ]
        ];
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $q4Max = $this->_conn->select();
        $mQ4Max = $this->_mockDbSelect();
        $mConn
            ->expects($this->at(0))
            ->method('select')
            ->willReturn($mQ4Max);
        // $query = $this->_conn->select();
        $mQuery = $this->_mockDbSelect();
        $mConn
            ->expects($this->at(1))
            ->method('select')
            ->willReturn($mQuery);
        // $this->_conn->insert($tbl, $data);
        $mConn
            ->expects($this->exactly(2))
            ->method('insert');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $sub Db */
        $sub = new Db($mLogger, $mDba, $mToolbox, $mCallRepo);
        $sub->saveCalculatedUpdates($UPDATES);
    }
}