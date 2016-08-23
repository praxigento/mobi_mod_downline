<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Service\ICustomer;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
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
    /** @var  \Mockery\MockInterface */
    private $mSubReferral;
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
        $this->mSubReferral = $this->_mock(Sub\Referral::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManTrans,
            $this->mRepoGeneric,
            $this->mRepoChange,
            $this->mRepoCustomer,
            $this->mSubReferral
        );
    }

    /**
     * @expectedException \Exception
     */
    public function test_add_commonNode_withCountry_exception()
    {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $PARENT_ID = 12;
        $REF_ID = '123123123';
        $DATE = '2015-12-05 12:34:56';
        $PATH = '/1/2/3/43/';
        $DEPTH = 4;
        $DO_CUST = new Customer();
        $DO_CUST->setPath($PATH);
        $DO_CUST->setDepth($DEPTH);
        $COUNTRY_CODE = 'LV';
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $parentId = $this->_subReferral->getReferredParentId($customerId, $parentId);
        $this->mSubReferral
            ->shouldReceive('getReferredParentId')->once()
            ->andReturn($PARENT_ID);
        // $data = $this->_repoCustomer->getById($parentId);
        $this->mRepoCustomer
            ->shouldReceive('getById')->once()
            ->andReturn($DO_CUST);
        // $idLog = $this->_repoChange->create($toLog);
        $this->mRepoChange
            ->shouldReceive('create')
            ->andThrow(new \Exception());
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Add();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setParentId($PARENT_ID);
        $req->setReference($REF_ID);
        $req->setCountryCode($COUNTRY_CODE);
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
        $COUNTRY_CODE = 'LV';
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $parentId = $this->_subReferral->getReferredParentId($customerId, $parentId);
        $this->mSubReferral
            ->shouldReceive('getReferredParentId')->once()
            ->andReturn($PARENT_ID);
        // $toAdd[Customer::ATTR_COUNTRY_CODE] = $this->_subReferral->getDefaultCountryCode();
        $this->mSubReferral
            ->shouldReceive('getDefaultCountryCode')->once()
            ->andReturn($COUNTRY_CODE);
        // $this->_repoCustomer->create($toAdd);
        $this->mRepoCustomer
            ->shouldReceive('create')->once();
        // $idLog = $this->_repoChange->create($toLog);
        $this->mRepoChange
            ->shouldReceive('create')->once()
            ->andReturn($ID_INSERTED_LOG);
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
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
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
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
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\ChangeParent();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setNewParentId($PARENT_ID_NEW);
        $req->setDate($DATE);
        $resp = $this->obj->changeParent($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_changeParent_nothingToDo()
    {
        /** === Test Data === */
        $DATE = '2015-12-05 12:34:56';
        $CUSTOMER_ID = 21;
        $PARENT_ID_CUR = 43;
        $PATH_CUR = '/1/2/3/43/';
        $DEPTH_CUR = 4;
        $PARENT_ID_NEW = $PARENT_ID_CUR;
        $DO_CUST = new Customer();
        $DO_CUST->setParentId($PARENT_ID_CUR);
        $DO_CUST->setPath($PATH_CUR);
        $DO_CUST->setDepth($DEPTH_CUR);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $data = $this->_repoCustomer->getById($customerId);
        $this->mRepoCustomer
            ->shouldReceive('getById')->once()
            ->andReturn($DO_CUST);
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\ChangeParent();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setNewParentId($PARENT_ID_NEW);
        $req->setDate($DATE);
        $resp = $this->obj->changeParent($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_changeParent_rootNode()
    {
        /** === Test Data === */
        $DATE = '2015-12-05 12:34:56';
        $CUSTOMER_ID = 21;
        $PARENT_ID_CUR = 43;
        $PATH_CUR = '/1/2/3/43/';
        $DEPTH_CUR = 4;
        $PARENT_ID_NEW = $CUSTOMER_ID;
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
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $data = $this->_repoCustomer->getById($customerId);
        $this->mRepoCustomer
            ->shouldReceive('getById')->once()
            ->andReturn($DO_CUST);
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
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
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

    public function test_generateReferralCode()
    {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        /** === Call and asserts  === */
        $req = new Request\GenerateReferralCode();
        $req->setCustomerId($CUSTOMER_ID);
        $resp = $this->obj->generateReferralCode($req);
        $this->assertTrue($resp->isSucceed());
    }
}