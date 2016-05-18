<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Praxigento\Downline\Data\Entity\Customer as Entity;
use Praxigento\Downline\Repo\Entity\ICustomer;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Customer_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Customer */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /** setup mocks for constructor */
        $mRsrc = $this->_mockResourceConnection($this->mConn);
        /** create object to test */
        $this->obj = new Customer(
            $mRsrc,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(ICustomer::class, $this->obj);
    }

    public function test_updateChildrenPath_negative()
    {
        /** === Test Data === */
        $PATH = '/1/2/3/';
        $REPLACE = '/3/2/';
        $DELTA = -1;
        $UPDATED = 3;
        /** === Setup Mocks === */
        // $qPath = $this->_conn->quote($path);
        // ...
        $this->mConn
            ->shouldReceive('quote');
        // $result = $this->_repoGeneric->updateEntity(Entity::ENTITY_NAME, $bind, $where);
        $this->mRepoGeneric
            ->shouldReceive('updateEntity')->once()
            ->andReturn($UPDATED);
        /** === Call and asserts  === */
        $res = $this->obj->updateChildrenPath($PATH, $REPLACE, $DELTA);
        $this->assertEquals($UPDATED, $res);

    }

    public function test_updateChildrenPath_positive()
    {
        /** === Test Data === */
        $PATH = '/1/2/3/';
        $REPLACE = '/4/3/2/1/';
        $DELTA = 1;
        $UPDATED = 3;
        /** === Setup Mocks === */
        // $qPath = $this->_conn->quote($path);
        // ...
        $this->mConn
            ->shouldReceive('quote');
        // $result = $this->_repoGeneric->updateEntity(Entity::ENTITY_NAME, $bind, $where);
        $this->mRepoGeneric
            ->shouldReceive('updateEntity')->once()
            ->andReturn($UPDATED);
        /** === Call and asserts  === */
        $res = $this->obj->updateChildrenPath($PATH, $REPLACE, $DELTA);
        $this->assertEquals($UPDATED, $res);
    }
}