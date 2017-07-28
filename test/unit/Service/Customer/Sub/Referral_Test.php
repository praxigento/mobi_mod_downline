<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Customer\Sub;

use Praxigento\Downline\Data\Entity\Customer;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Referral_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mRepoCustomer;
    /** @var  \Mockery\MockInterface */
    private $mToolReferral;
    /** @var  Referral */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mRepoCustomer = $this->_mock(\Praxigento\Downline\Repo\Entity\Customer::class);
        $this->mToolReferral = $this->_mock(\Praxigento\Downline\Tool\IReferral::class);
        /** create object to test */
        $this->obj = new Referral(
            $this->mRepoCustomer,
            $this->mToolReferral
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Referral::class, $this->obj);
    }

    public function test_getDefaultCountryCode()
    {
        /** === Test Data === */
        $COUNTRY_CODE = 'LV';
        /** === Setup Mocks === */
        $this->mToolReferral
            ->shouldReceive('getDefaultCountryCode')->once()
            ->andReturn($COUNTRY_CODE);
        /** === Call and asserts  === */
        $res = $this->obj->getDefaultCountryCode();
        $this->assertEquals($COUNTRY_CODE, $res);
    }

    public function test_getReferredParentId()
    {
        /** === Test Data === */
        $CUST_ID = 321;
        $PARENT_ID = 345;
        $PARENT_ID_REF = 793;
        $CUST_DO = new Customer([
            Customer::ATTR_CUSTOMER_ID => $PARENT_ID_REF
        ]);
        $REF_CODE = 'referral code';
        /** === Setup Mocks === */
        // $code = $this->_toolReferral->getReferralCode();
        $this->mToolReferral
            ->shouldReceive('getReferralCode')->once()
            ->andReturn($REF_CODE);
        // $parentDo = $this->_repoCustomer->getByReferralCode($code);
        $this->mRepoCustomer
            ->shouldReceive('getByReferralCode')->once()
            ->andReturn($CUST_DO);
        /** === Call and asserts  === */
        $res = $this->obj->getReferredParentId($CUST_ID, $PARENT_ID);
        $this->assertEquals($PARENT_ID_REF, $res);
    }
}