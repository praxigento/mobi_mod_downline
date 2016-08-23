<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Customer_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  Customer */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Customer();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $COUNTRY_CODE = 'country code';
        $CUSTOMER_ID = 'cust id';
        $DEPTH = 'depth';
        $HUMAN_REF = 'mlm id';
        $PARENT_ID = 'parent id';
        $PATH = 'path';
        $REFERRAL_CODE = 'referral_code';
        /** === Call and asserts  === */
        $this->obj->setCountryCode($COUNTRY_CODE);
        $this->obj->setCustomerId($CUSTOMER_ID);
        $this->obj->setDepth($DEPTH);
        $this->obj->setHumanRef($HUMAN_REF);
        $this->obj->setParentId($PARENT_ID);
        $this->obj->setPath($PATH);
        $this->obj->setReferralCode($REFERRAL_CODE);
        $this->assertEquals($COUNTRY_CODE, $this->obj->getCountryCode());
        $this->assertEquals($CUSTOMER_ID, $this->obj->getCustomerId());
        $this->assertEquals($DEPTH, $this->obj->getDepth());
        $this->assertEquals($HUMAN_REF, $this->obj->getHumanRef());
        $this->assertEquals($PARENT_ID, $this->obj->getParentId());
        $this->assertEquals($PATH, $this->obj->getPath());
        $this->assertEquals($REFERRAL_CODE, $this->obj->getReferralCode());
    }
}