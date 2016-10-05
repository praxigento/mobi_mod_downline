<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Account_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo
{

    /** @var  Account */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mFactorySelect = $this->_mock(Account\SelectFactory::class);
        /** create object to test */
        $this->obj = new Account(
            $this->mResource,
            $this->mFactorySelect
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Account::class, $this->obj);
    }
}