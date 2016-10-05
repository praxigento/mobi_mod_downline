<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg\Def\Account;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');
use Praxigento\Accounting\Data\Entity\Account as EAccount;
use Praxigento\Accounting\Data\Entity\Type\Asset as ETypeAsset;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Data\Entity\Customer as ECustomer;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class SelectFactory_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Agg\SelectFactory
{
    /** @var  SelectFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new SelectFactory(
            $this->mResource
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SelectFactory::class, $this->obj);
    }

    public function test_getQueryToSelect()
    {
        /** === Test Data === */
        $sql = "SELECT `paa`.`id` AS `Id`, `paa`.`balance` AS `Balance`, `pata`.`code` AS `Asset`, (CONCAT(firstname, ' ', lastname)) AS `CustName`, `ce`.`email` AS `CustEmail`, `pdc`.`human_ref` AS `Ref` FROM `prxgt_acc_account` AS `paa`
 LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON pata.id=paa.asset_type_id
 LEFT JOIN `customer_entity` AS `ce` ON ce.entity_id=paa.customer_id
 LEFT JOIN `prxgt_dwnl_customer` AS `pdc` ON pdc.customer_id=paa.customer_id";
        /** === Setup Mocks === */
        $this->_setTableNames([
            ECustomer::ENTITY_NAME,
            EAccount::ENTITY_NAME,
            Cfg::ENTITY_MAGE_CUSTOMER,
            ETypeAsset::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertEquals($sql, (string)$res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Test Data === */
        $sql = 'SELECT (COUNT(paa.id)), `pdc`.`human_ref` AS `Ref` FROM `prxgt_acc_account` AS `paa`
 LEFT JOIN `prxgt_acc_type_asset` AS `pata` ON pata.id=paa.asset_type_id
 LEFT JOIN `customer_entity` AS `ce` ON ce.entity_id=paa.customer_id
 LEFT JOIN `prxgt_dwnl_customer` AS `pdc` ON pdc.customer_id=paa.customer_id';
        /** === Setup Mocks === */
        $this->_setTableNames([
            ECustomer::ENTITY_NAME,
            EAccount::ENTITY_NAME,
            Cfg::ENTITY_MAGE_CUSTOMER,
            ETypeAsset::ENTITY_NAME
        ]);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertEquals($sql, (string)$res);
    }
}