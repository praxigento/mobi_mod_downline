<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Observer;


include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Register_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mCallCustomer;
    /** @var  CustomerSaveAfterDataObject */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mCallCustomer = $this->_mock(\Praxigento\Downline\Service\ICustomer::class);
        /** create object to test */
        $this->obj = new CustomerSaveAfterDataObject(
            $this->mCallCustomer
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(CustomerSaveAfterDataObject::class, $this->obj);
    }

    public function test_execute()
    {
        /** === Test Data === */
        $ID_BEFORE = null;
        $ID_AFTER = 321;
        /** === Setup Mocks === */
        $mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        // $beforeSave = $observer->getData('orig_customer_data_object');
        $mBeforeSave = $this->_mock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $mObserver
            ->shouldReceive('getData')->once()
            ->andReturn($mBeforeSave);
        // $afterSave = $observer->getData('customer_data_object');
        $mAfterSave = $this->_mock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $mObserver
            ->shouldReceive('getData')->once()
            ->andReturn($mAfterSave);
        // $idBefore = $beforeSave->getId();
        $mBeforeSave->shouldReceive('getId')->once()
            ->andReturn($ID_BEFORE);
        // $idAfter = $afterSave->getId();
        $mAfterSave->shouldReceive('getId')->once()
            ->andReturn($ID_AFTER);
        // $this->_callCustomer->add($req);
        $this->mCallCustomer
            ->shouldReceive('add')->once();
        /** === Call and asserts  === */
        $this->obj->execute($mObserver);
    }
}