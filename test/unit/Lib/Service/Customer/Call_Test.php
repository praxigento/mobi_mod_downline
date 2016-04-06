<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Customer;

use Praxigento\Core\Service\Base\Response as BaseResponse;
use Praxigento\Core\Lib\Service\Repo\Response\AddEntity as AddEntityResponse;
use Praxigento\Core\Lib\Service\Repo\Response\UpdateEntity as UpdateEntityResponse;
use Praxigento\Downline\Data\Entity\Customer;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {

    public function test_add_common_commit() {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $PARENT_ID = 12;
        $REF_ID = '123123123';
        $DATE = '2015-12-05 12:34:56';
        $PARENT_PATH = '/1/2/3/';
        $PARENT_DEPTH = 3;
        $ID_INSERTED_CUST = 1024;
        $ID_INSERTED_LOG = 2048;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mRespByPk = new BaseResponse();
        $mRespByPk->setData([
            Customer::ATTR_PATH  => $PARENT_PATH,
            Customer::ATTR_DEPTH => $PARENT_DEPTH
        ]);
        $mRespByPk->setAsSucceed();
        $mCallRepo
            ->expects($this->once())
            ->method('getEntityByPk')
            ->willReturn($mRespByPk);
        // $respAdd = $this->_callRepo->addEntity($reqAdd);
        $mRespAddCust = new AddEntityResponse();
        $mRespAddCust->setData([
            AddEntityResponse::ID_INSERTED => $ID_INSERTED_CUST
        ]);
        $mRespAddCust->setAsSucceed();
        $mCallRepo
            ->expects($this->at(1))
            ->method('addEntity')
            ->willReturn($mRespAddCust);
        // $respLog = $this->_callRepo->addEntity($reqLog);
        $mRespAddLog = new AddEntityResponse();
        $mRespAddLog->setData([
            AddEntityResponse::ID_INSERTED => $ID_INSERTED_LOG
        ]);
        $mRespAddLog->setAsSucceed();
        $mCallRepo
            ->expects($this->at(2))
            ->method('addEntity')
            ->willReturn($mRespAddLog);
        // $this->_conn->commit();
        $mConn
            ->expects($this->once())
            ->method('commit');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\Add();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setParentId($PARENT_ID);
        $req->setReference($REF_ID);
        $req->setDate($DATE);
        $resp = $call->add($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_add_root_customerInsertFailed() {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $PARENT_ID = 21;
        $REF_ID = '123123123';
        $DATE = '2015-12-05 12:34:56';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respAdd = $this->_callRepo->addEntity($reqAdd);
        $mRespAddCust = new AddEntityResponse();
        $mCallRepo
            ->expects($this->at(0))
            ->method('addEntity')
            ->willReturn($mRespAddCust);
        // $this->_conn->rollBack();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\Add();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setParentId($PARENT_ID);
        $req->setReference($REF_ID);
        $req->setDate($DATE);
        $resp = $call->add($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_add_root_changeLogInsertFailed() {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $PARENT_ID = 21;
        $REF_ID = '123123123';
        $DATE = '2015-12-05 12:34:56';
        $ID_INSERTED_CUST = 1024;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respAdd = $this->_callRepo->addEntity($reqAdd);
        $mRespAddCust = new AddEntityResponse();
        $mRespAddCust->setData([
            AddEntityResponse::ID_INSERTED => $ID_INSERTED_CUST
        ]);
        $mRespAddCust->setAsSucceed();
        $mCallRepo
            ->expects($this->at(0))
            ->method('addEntity')
            ->willReturn($mRespAddCust);
        // $respLog = $this->_callRepo->addEntity($reqLog);
        $mRespAddLog = new AddEntityResponse();
        $mCallRepo
            ->expects($this->at(1))
            ->method('addEntity')
            ->willReturn($mRespAddLog);
        // $this->_conn->rollBack();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\Add();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setParentId($PARENT_ID);
        $req->setReference($REF_ID);
        $req->setDate($DATE);
        $resp = $call->add($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_add_common_exception() {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $PARENT_ID = 12;
        $REF_ID = '123123123';
        $DATE = '2015-12-05 12:34:56';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mCallRepo
            ->expects($this->once())
            ->method('getEntityByPk')
            ->willThrowException(new \Exception());
        // $this->_conn->rollBack();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\Add();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setParentId($PARENT_ID);
        $req->setReference($REF_ID);
        $req->setDate($DATE);
        $resp = $call->add($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_changeParent_common_commit() {
        /** === Test Data === */
        $DATE = '2015-12-05 12:34:56';
        $CUSTOMER_ID = 21;
        $PARENT_ID_OLD = 10;
        $PARENT_PATH_OLD = '/1/2/3/';
        $PARENT_DEPTH_OLD = 3;
        $PARENT_ID_NEW = 12;
        $PARENT_PATH_NEW = '/3/2/1/';
        $PARENT_DEPTH_NEW = 5;
        $ID_INSERTED_LOG = 2048;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mRespByPk = new BaseResponse();
        $mRespByPk->setData([
            Customer::ATTR_PARENT_ID => $PARENT_ID_OLD,
            Customer::ATTR_PATH      => $PARENT_PATH_OLD,
            Customer::ATTR_DEPTH     => $PARENT_DEPTH_OLD
        ]);
        $mRespByPk->setAsSucceed();
        $mCallRepo
            ->expects($this->at(0))
            ->method('getEntityByPk')
            ->willReturn($mRespByPk);
        /* get new parent data */
        //  $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mRespByPkParent = new BaseResponse();
        $mRespByPkParent->setData([
            Customer::ATTR_PARENT_ID => $PARENT_ID_NEW,
            Customer::ATTR_PATH      => $PARENT_PATH_NEW,
            Customer::ATTR_DEPTH     => $PARENT_DEPTH_NEW
        ]);
        $mRespByPkParent->setAsSucceed();
        $mCallRepo
            ->expects($this->at(1))
            ->method('getEntityByPk')
            ->willReturn($mRespByPkParent);
        // $respUpdate = $this->_callRepo->updateEntity($reqUpdate);
        $mRespUpdate = new UpdateEntityResponse();
        $mRespUpdate->setData([ UpdateEntityResponse::ROWS_UPDATED => 1 ]);
        $mRespUpdate->setAsSucceed();
        $mCallRepo
            ->expects($this->at(2))
            ->method('updateEntity')
            ->willReturn($mRespUpdate);
        // $respUpdate = $this->_callRepo->updateEntity($reqUpdate);
        $mRespUpdate = new UpdateEntityResponse();
        $mRespUpdate->setData([ UpdateEntityResponse::ROWS_UPDATED => 5 ]);
        $mRespUpdate->setAsSucceed();
        $mCallRepo
            ->expects($this->at(3))
            ->method('updateEntity')
            ->willReturn($mRespUpdate);
        // $respAdd = $this->_callRepo->addEntity($reqAdd);
        $mRespAddCust = new AddEntityResponse();
        $mRespAddCust->setData([
            AddEntityResponse::ID_INSERTED => $ID_INSERTED_LOG
        ]);
        $mRespAddCust->setAsSucceed();
        $mCallRepo
            ->expects($this->at(4))
            ->method('addEntity')
            ->willReturn($mRespAddCust);
        // $this->_conn->commit();
        $mConn
            ->expects($this->once())
            ->method('commit');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\ChangeParent();
        $req->setData(Request\ChangeParent::CUSTOMER_ID, $CUSTOMER_ID);
        $req->setData(Request\ChangeParent::PARENT_ID_NEW, $PARENT_ID_NEW);
        $req->setData(Request\ChangeParent::DATE, $DATE);
        $resp = $call->changeParent($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_changeParent_nothingToChange() {
        /** === Test Data === */
        $DATE = '2015-12-05 12:34:56';
        $CUSTOMER_ID = 21;
        $PARENT_ID_OLD = 10;
        $PARENT_PATH_OLD = '/1/2/3/';
        $PARENT_DEPTH_OLD = 3;
        $PARENT_ID_NEW = 10;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mRespByPk = new BaseResponse();
        $mRespByPk->setData([
            Customer::ATTR_PARENT_ID => $PARENT_ID_OLD,
            Customer::ATTR_PATH      => $PARENT_PATH_OLD,
            Customer::ATTR_DEPTH     => $PARENT_DEPTH_OLD
        ]);
        $mCallRepo
            ->expects($this->at(0))
            ->method('getEntityByPk')
            ->willReturn($mRespByPk);
        // $this->_conn->commit();
        $mConn
            ->expects($this->once())
            ->method('commit');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\ChangeParent();
        $req->setData(Request\ChangeParent::CUSTOMER_ID, $CUSTOMER_ID);
        $req->setData(Request\ChangeParent::PARENT_ID_NEW, $PARENT_ID_NEW);
        $req->setData(Request\ChangeParent::DATE, $DATE);
        $resp = $call->changeParent($req);
        $this->assertTrue($resp->isSucceed());
    }


    public function test_changeParent_rootNode_failedParentUpdate() {
        /** === Test Data === */
        $DATE = '2015-12-05 12:34:56';
        $CUSTOMER_ID = 21;
        $PARENT_ID_OLD = 10;
        $PARENT_PATH_OLD = '/1/2/3/';
        $PARENT_DEPTH_OLD = 3;
        $PARENT_ID_NEW = 21;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mRespByPk = new BaseResponse();
        $mRespByPk->setData([
            Customer::ATTR_PARENT_ID => $PARENT_ID_OLD,
            Customer::ATTR_PATH      => $PARENT_PATH_OLD,
            Customer::ATTR_DEPTH     => $PARENT_DEPTH_OLD
        ]);
        $mCallRepo
            ->expects($this->at(0))
            ->method('getEntityByPk')
            ->willReturn($mRespByPk);
        // $respUpdate = $this->_callRepo->updateEntity($reqUpdate);
        $mRespUpdate = new UpdateEntityResponse();
        $mCallRepo
            ->expects($this->at(1))
            ->method('updateEntity')
            ->willReturn($mRespUpdate);
        // $this->_conn->rollBack();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\ChangeParent();
        $req->setData(Request\ChangeParent::CUSTOMER_ID, $CUSTOMER_ID);
        $req->setData(Request\ChangeParent::PARENT_ID_NEW, $PARENT_ID_NEW);
        $req->setData(Request\ChangeParent::DATE, $DATE);
        $resp = $call->changeParent($req);
        $this->assertFalse($resp->isSucceed());
    }

    public function test_changeParent_exception() {
        /** === Test Data === */
        $DATE = '2015-12-05 12:34:56';
        $CUSTOMER_ID = 21;
        $PARENT_ID_NEW = 21;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
        $mCallRepo
            ->expects($this->at(0))
            ->method('getEntityByPk')
            ->willThrowException(new \Exception());
        // $this->_conn->rollBack();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo);
        $req = new Request\ChangeParent();
        $req->setData(Request\ChangeParent::CUSTOMER_ID, $CUSTOMER_ID);
        $req->setData(Request\ChangeParent::PARENT_ID_NEW, $PARENT_ID_NEW);
        $req->setData(Request\ChangeParent::DATE, $DATE);
        $resp = $call->changeParent($req);
        $this->assertFalse($resp->isSucceed());
    }
}