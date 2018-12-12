<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Manual\Praxigento\Downline\Service\Customer\Downline;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

use Praxigento\Downline\Service\Customer\Downline\StickUp as AService;
use Praxigento\Downline\Service\Customer\Downline\StickUp\Request as ARequest;
use Praxigento\Downline\Service\Customer\Downline\StickUp\Response as AResponse;

class StickUpTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_exec()
    {
        /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans */
        $manTrans = $this->manObj->get(\Praxigento\Core\Api\App\Repo\Transaction\Manager::class);
        $def = $manTrans->begin();
        /** @var AService $obj */
        $obj = $this->manObj->get(AService::class);
        $req = new ARequest();
        $req->setCustomerId(380);
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
        $manTrans->rollback($def);
    }

}