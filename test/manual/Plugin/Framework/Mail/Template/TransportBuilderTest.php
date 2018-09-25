<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Downline\Plugin\Magento\Framework\Mail\Template;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class TransportBuilderTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_beforeSetTemplateVars()
    {
        $this->setAreaCode();
        /** @var \Magento\Customer\Model\EmailNotification $notification */
        $notification = $this->manObj->get(\Magento\Customer\Model\EmailNotification::class);
        /** @var \Magento\Customer\Api\CustomerRepositoryInterface $repo */
        $repo = $this->manObj->get(\Magento\Customer\Api\CustomerRepositoryInterface::class);
        $cust = $repo->getById(63);
        $notification->newAccount($cust);
        $this->assertNotNull($notification);
    }

}