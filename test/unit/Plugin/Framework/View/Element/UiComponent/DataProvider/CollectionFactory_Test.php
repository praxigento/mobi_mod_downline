<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Framework\View\Element\UiComponent\DataProvider;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class CollectionFactory_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mSubQueryModifier;
    /** @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory */
    private $mSubject;
    /** @var  CollectionFactory */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory::class);
        $this->mSubQueryModifier = $this->_mock(Sub\QueryModifier::class);
        /** create object to test */
        $this->obj = new CollectionFactory(
            $this->mSubQueryModifier
        );
    }

    public function test_aroundGetReport()
    {
        /** === Test Data === */
        $REQUEST_NAME = 'customer_listing_data_source';
        /** === Setup Mocks === */
        $mResult = $this->_mock(\Magento\Customer\Model\ResourceModel\Grid\Collection::class);
        $mProceed = function () use ($mResult) {
            return $mResult;
        };
        // $this->_subQueryModifier->populateSelect($result);
        $this->mSubQueryModifier
            ->shouldReceive('populateSelect')->once();
        // $this->_subQueryModifier->addFieldsMapping($result);
        $this->mSubQueryModifier
            ->shouldReceive('addFieldsMapping')->once();
        /** === Call and asserts  === */
        $res = $this->obj->aroundGetReport(
            $this->mSubject,
            $mProceed,
            $REQUEST_NAME
        );
        $this->assertTrue($res instanceof \Magento\Customer\Model\ResourceModel\Grid\Collection);
    }

    public function test_constructor()
    {
        $this->assertInstanceOf(CollectionFactory::class, $this->obj);
    }
}