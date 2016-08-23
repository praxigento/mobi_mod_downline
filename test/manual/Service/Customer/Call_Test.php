<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Customer;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery
{

    public function test_add()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Service\Customer\Call */
        $call = $obm->get('Praxigento\Downline\Service\Customer\Call');
        $request = new Request\Add();
        $request->customerId = 159;
        $request->parentId = 158;
        $request->humanReference = '123';
        $response = $call->add($request);
        $this->assertTrue($response->isSucceed());
    }

    public function test_changeParent()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Service\ICustomer */
        $call = $obm->get(\Praxigento\Downline\Service\ICustomer::class);
        $request = new Request\ChangeParent();
        $request->setCustomerId(6);
        $request->setNewParentId(1);
        $response = $call->changeParent($request);
        $this->assertTrue($response->isSucceed());
    }

}