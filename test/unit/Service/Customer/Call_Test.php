<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Service\ICustomer;

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
    private $mRepoCustomer;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->mRepoChange = $this->_mock(\Praxigento\Downline\Repo\Entity\IChange::class);
        $this->mRepoCustomer = $this->_mock(\Praxigento\Downline\Repo\Entity\ICustomer::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManTrans,
            $this->mRepoGeneric,
            $this->mRepoChange,
            $this->mRepoCustomer
        );
    }

    /**
     * @expectedException \Exception
     */
    public function test_add_commonNode_exception()
    {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $PARENT_ID = 12;
        $REF_ID = '123123123';
        $DATE = '2015-12-05 12:34:56';
        /** === Setup Mocks === */
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        // $this->_repoCustomer->create($toAdd);
        $this->mRepoCustomer
            ->shouldReceive('create')->once();
        // $idLog = $this->_repoChange->create($toLog);
        $this->mRepoChange
            ->shouldReceive('create')->once()
            ->andThrow(new \Exception());
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once();
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setParentId($PARENT_ID);
        $req->setReference($REF_ID);
        $req->setDate($DATE);
        $this->obj->add($req);
    }


    public function test_add_rootNode_commit()
    {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $PARENT_ID = $CUSTOMER_ID;
        $REF_ID = '123123123';
        $DATE = '2015-12-05 12:34:56';
        $ID_INSERTED_LOG = 2048;
        /** === Setup Mocks === */
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        // $this->_repoCustomer->create($toAdd);
        $this->mRepoCustomer
            ->shouldReceive('create')->once();
        // $idLog = $this->_repoChange->create($toLog);
        $this->mRepoChange
            ->shouldReceive('create')->once()
            ->andReturn($ID_INSERTED_LOG);
        // $this->_manTrans->transactionCommit($trans);
        $this->mManTrans
            ->shouldReceive('transactionCommit')->once();
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once();
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setParentId($PARENT_ID);
        $req->setReference($REF_ID);
        $req->setDate($DATE);
        $resp = $this->obj->add($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_changeParent()
    {
        /** === Test Data === */
        $DATE = '2015-12-05 12:34:56';
        $CUSTOMER_ID = 21;
        $PARENT_ID_CUR = 43;
        $PATH_CUR = '/1/2/3/43/';
        $DEPTH_CUR = 4;
        $PARENT_ID_NEW = 55;
        $PATH_NEW = '/1/2/55/';
        $DEPTH_NEW = 3;
        $DO_CUST = new Customer();
        $DO_CUST->setParentId($PARENT_ID_CUR);
        $DO_CUST->setPath($PATH_CUR);
        $DO_CUST->setDepth($DEPTH_CUR);
        $DO_PARENT = new Customer();
        $DO_PARENT->setPath($PATH_NEW);
        $DO_PARENT->setDepth($DEPTH_NEW);
        /** === Setup Mocks === */
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        // $data = $this->_repoCustomer->getById($customerId);
        $this->mRepoCustomer
            ->shouldReceive('getById')->once()
            ->andReturn($DO_CUST);
        // $newParentData = $this->_repoCustomer->getById($newParentId);
        $this->mRepoCustomer
            ->shouldReceive('getById')->once()
            ->andReturn($DO_PARENT);
        // $updateRows = $this->_repoCustomer->updateById($customerId, $bind);
        $this->mRepoCustomer
            ->shouldReceive('updateById')->once()
            ->andReturn(1);
        // $rowsUpdated = $this->_repoCustomer->updateChildrenPath($pathKey, $pathReplace, $deltaDepth);
        $this->mRepoCustomer
            ->shouldReceive('updateChildrenPath')->once()
            ->andReturn(2);
        // $insertedId = $this->_repoChange->create($bind);
        $this->mRepoChange
            ->shouldReceive('create')->once()
            ->andReturn(433);
        // $this->_manTrans->transactionCommit($trans);
        $this->mManTrans
            ->shouldReceive('transactionCommit')->once();
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once();
        /** === Call and asserts  === */
        $req = new Request\ChangeParent();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setNewParentId($PARENT_ID_NEW);
        $req->setDate($DATE);
        $resp = $this->obj->changeParent($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(ICustomer::class, $this->obj);
    }
}