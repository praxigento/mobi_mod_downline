<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Lib\Service\Map;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{

    /** @var  Call */
    private $call;

    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Test is deprecated after M1 & M2 merge is done.');
        $this->call = new Call();
    }

    public function test_byId()
    {
        /** === Test Data === */
        $AS_ID = 'id';
        $AS_OTHER = 'other';
        $ID = 23;
        $OTHER = 'any data';
        $DATA = [[$AS_ID => $ID, $AS_OTHER => $OTHER]];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $req = new Request\ById();
        $req->setAsId($AS_ID);
        $req->setDataToMap($DATA);
        $resp = $this->call->byId($req);
        $this->assertTrue($resp->isSucceed());
        $mapped = $resp->getMapped();
        $this->assertTrue(is_array($mapped));
        $this->assertEquals($OTHER, $mapped[$ID][$AS_OTHER]);
    }

    public function test_treeByDepth()
    {
        /** === Test Data === */
        $AS_CUST_ID = 'id';
        $AS_DEPTH = 'depth';
        $SHOULD_REVERSE = true;
        $DEPTH_0 = 0;
        $DEPTH_1 = 1;
        $DATA = [
            [$AS_CUST_ID => 1, $AS_DEPTH => $DEPTH_0],
            [$AS_CUST_ID => 2, $AS_DEPTH => $DEPTH_0],
            [$AS_CUST_ID => 3, $AS_DEPTH => $DEPTH_1]
        ];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $req = new Request\TreeByDepth();
        $req->setDataToMap($DATA);
        $req->setAsCustomerId($AS_CUST_ID);
        $req->setAsDepth($AS_DEPTH);
        $req->setShouldReversed($SHOULD_REVERSE);
        $resp = $this->call->treeByDepth($req);
        $this->assertTrue($resp->isSucceed());
        $mapped = $resp->getMapped();
        $this->assertTrue(is_array($mapped));
        $this->assertEquals(2, count($mapped[$DEPTH_0]));
        $this->assertEquals(1, count($mapped[$DEPTH_1]));
    }

    public function test_treeByTeams()
    {
        /** === Test Data === */
        $AS_CUST_ID = 'customer';
        $AS_PARENT_ID = 'parent';
        $ID_1 = 1;
        $ID_2 = 2;
        $ID_3 = 3;
        $DATA = [
            [$AS_CUST_ID => $ID_1, $AS_PARENT_ID => $ID_1],
            [$AS_CUST_ID => $ID_2, $AS_PARENT_ID => $ID_1],
            [$AS_CUST_ID => $ID_3, $AS_PARENT_ID => $ID_2]
        ];
        /** === Setup Mocks === */
        /** === Call and asserts  === */
        $req = new Request\TreeByTeams();
        $req->setDataToMap($DATA);
        $req->setAsCustomerId($AS_CUST_ID);
        $req->setAsParentId($AS_PARENT_ID);
        $resp = $this->call->treeByTeams($req);
        $this->assertTrue($resp->isSucceed());
        $mapped = $resp->getMapped();
        $this->assertTrue(is_array($mapped));
        $this->assertEquals(2, count($mapped));
        $custId = reset($mapped[$ID_1]);
        $this->assertEquals($ID_2, $custId);
        $custId = reset($mapped[$ID_2]);
        $this->assertEquals($ID_3, $custId);
    }

}