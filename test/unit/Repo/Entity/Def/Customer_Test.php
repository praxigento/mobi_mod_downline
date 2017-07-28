<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Praxigento\Downline\Data\Entity\Customer as Entity;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Customer_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  Customer */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Customer(
            $this->mResource,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Downline\Repo\Entity\Def\Customer::class, $this->obj);
    }

    public function test_getByReferralCode()
    {
        /** === Test Data === */
        $CODE = 'code';
        $CUST_ID = 321;
        $ITEMS = [
            [Entity::ATTR_CUSTOMER_ID => $CUST_ID]
        ];
        /** === Setup Mocks === */
        // $qCode = $this->_conn->quote($code);
        $this->mConn
            ->shouldReceive('quote')->once()
            ->andReturn("'$CODE'");
        // $items = $this->_repoGeneric->getEntities(Entity::ENTITY_NAME, $cols, $where);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')->once()
            ->andReturn($ITEMS);
        // $result = $this->createEntity($data);
        /** === Call and asserts  === */
        $res = $this->obj->getByReferralCode($CODE);
        $this->assertEquals($CUST_ID, $res->getCustomerId());
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