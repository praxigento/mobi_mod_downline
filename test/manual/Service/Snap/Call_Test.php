<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap;

use Praxigento\Core\Lib\Context;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_getLastDate() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $call \Praxigento\Downline\Lib\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Lib\Service\Snap\Call');
        $req = new Request\GetLastDate();
        /** @var  $resp Response\GetLastDate */
        $resp = $call->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $period = $resp->getLastDate();
        $this->assertNotNull($period);
    }

    public function test_getStateOnDate() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $call \Praxigento\Downline\Lib\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Lib\Service\Snap\Call');
        $req = new Request\GetStateOnDate();
        $req->setDatestamp('20151202');
        /** @var  $resp Response\GetStateOnDate */
        $resp = $call->getStateOnDate($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_calc() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $call \Praxigento\Downline\Lib\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Lib\Service\Snap\Call');
        $req = new Request\Calc();
        $req->setDatestampTo('20151219');
        /** @var  $resp Response\Calc */
        $resp = $call->calc($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_extendMinimal() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $call \Praxigento\Downline\Lib\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Lib\Service\Snap\Call');
        $req = new Request\ExpandMinimal();
        $req->setTree([
            2  => 1,
            3  => 1,
            4  => 2,
            5  => 2,
            6  => 3,
            7  => 3,
            20 => 20,
            10 => 7,
            11 => 7,
            1  => 1,
            12 => 10
        ]);
        /** @var  $resp Response\Calc */
        $resp = $call->expandMinimal($req);
        $this->assertTrue($resp->isSucceed());
    }

}