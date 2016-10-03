<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Map;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Call_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Service\Call
{

    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function test_byId()
    {
        /** === Test Data === */
        $asId = 'id';
        $asOther = 'other';
        $id = 23;
        $other = 'any data';
        $data = [[$asId => $id, $asOther => $other]];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $req = new Request\ById();
        $req->setAsId($asId);
        $req->setDataToMap($data);
        $resp = $this->obj->byId($req);
        $this->assertTrue($resp->isSucceed());
        $mapped = $resp->getMapped();
        $this->assertTrue(is_array($mapped));
        $this->assertEquals($other, $mapped[$id][$asOther]);
    }

    public function test_constructor()
    {
        $this->assertInstanceOf(\Praxigento\Downline\Service\IMap::class, $this->obj);
    }

    public function test_treeByDepth()
    {
        /** === Test Data === */
        $asCustId = 'id';
        $asDepth = 'depth';
        $shoulrReverse = true;
        $depth0 = 0;
        $depth1 = 1;
        $data = [
            [$asCustId => 1, $asDepth => $depth0],
            [$asCustId => 2, $asDepth => $depth0],
            [$asCustId => 3, $asDepth => $depth1]
        ];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $req = new Request\TreeByDepth();
        $req->setDataToMap($data);
        $req->setAsCustomerId($asCustId);
        $req->setAsDepth($asDepth);
        $req->setShouldReversed($shoulrReverse);
        $resp = $this->obj->treeByDepth($req);
        $this->assertTrue($resp->isSucceed());
        $mapped = $resp->getMapped();
        $this->assertTrue(is_array($mapped));
        $this->assertEquals(2, count($mapped[$depth0]));
        $this->assertEquals(1, count($mapped[$depth1]));
    }

    public function test_treeByTeams()
    {
        /** === Test Data === */
        $asCustId = 'customer';
        $asParentId = 'parent';
        $id1 = 1;
        $id2 = 2;
        $id3 = 3;
        $data = [
            [$asCustId => $id1, $asParentId => $id1],
            [$asCustId => $id2, $asParentId => $id1],
            [$asCustId => $id3, $asParentId => $id2]
        ];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $req = new Request\TreeByTeams();
        $req->setDataToMap($data);
        $req->setAsCustomerId($asCustId);
        $req->setAsParentId($asParentId);
        $resp = $this->obj->treeByTeams($req);
        $this->assertTrue($resp->isSucceed());
        $mapped = $resp->getMapped();
        $this->assertTrue(is_array($mapped));
        $this->assertEquals(2, count($mapped));
        $custId = reset($mapped[$id1]);
        $this->assertEquals($id2, $custId);
        $custId = reset($mapped[$id2]);
        $this->assertEquals($id3, $custId);
    }

}