<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Customer;



include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {

    public function test_add() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Lib\Service\Customer\Call */
        $call = $obm->get('Praxigento\Downline\Lib\Service\Customer\Call');
        $request = new Request\Add();
        $request->customerId = 159;
        $request->parentId = 158;
        $request->humanReference = '123';
        $response = $call->add($request);
        $this->assertTrue($response->isSucceed());
    }

    public function test_changeParent() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Lib\Service\Customer\Call */
        $call = $obm->get('Praxigento\Downline\Lib\Service\Customer\Call');
        $request = new Request\ChangeParent();
        $request->customerId = 120;
        $request->parentIdNew = 119;
        $response = $call->changeParent($request);
        $this->assertTrue($response->isSucceed());
    }

}