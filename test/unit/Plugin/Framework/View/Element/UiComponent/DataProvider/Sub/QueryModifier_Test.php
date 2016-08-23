<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub;

include_once(__DIR__ . '/../../../../../../../phpunit_bootstrap.php');

class QueryModifier_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  QueryModifier */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        /** create object to test */
        $this->obj = new QueryModifier(
            $this->mResource
        );
    }

    public function test_addFieldsMapping()
    {
        /** === Test Data === */
        $mCollection = $this->_mock(\Magento\Customer\Model\ResourceModel\Grid\Collection::class);
        /** === Setup Mocks === */
        // $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $mCollection
            ->shouldReceive('addFilterToMap')->times(4);
        /** === Call and asserts  === */
        $this->obj->addFieldsMapping($mCollection);
    }

    public function test_constructor()
    {
        $this->assertInstanceOf(QueryModifier::class, $this->obj);
    }

    public function test_populateSelect()
    {
        /** === Test Data === */
        $TBL_CUSTOMER = 'customer table';
        $mCollection = $this->_mock(\Magento\Customer\Model\ResourceModel\Grid\Collection::class);
        /** === Setup Mocks === */
        // $select = $collection->getSelect();
        $mSelect = $this->_mockDbSelect(['joinLeft']);
        $mCollection
            ->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);
        // $tbl = [self::AS_TBL_CUST => $this->_resource->getTableName(Customer::ENTITY_NAME)];
        // $tbl = [self::AS_TBL_PARENT_CUST => $this->_resource->getTableName(Customer::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->twice()
            ->with(\Praxigento\Downline\Data\Entity\Customer::ENTITY_NAME)
            ->andReturn($TBL_CUSTOMER);

        /** === Call and asserts  === */
        $this->obj->populateSelect($mCollection);
    }

}