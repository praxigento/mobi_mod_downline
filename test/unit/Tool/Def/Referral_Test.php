<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Tool\Def;

use Praxigento\Downline\Tool\IReferral;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Referral_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mCookieManager;
    /** @var  \Mockery\MockInterface */
    private $mRegistry;
    /** @var  \Mockery\MockInterface */
    private $mToolDate;
    /** @var  Referral */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mCookieManager = $this->_mock(\Magento\Framework\Stdlib\CookieManagerInterface::class);
        $this->mRegistry = $this->_mock(\Magento\Framework\Registry::class);
        $this->mToolDate = $this->_mock(\Praxigento\Core\Tool\IDate::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Referral(
            $this->mCookieManager,
            $this->mRegistry,
            $this->mToolDate
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IReferral::class, $this->obj);
    }

    public function test_getDefaultCountryCode()
    {
        /** === Test Data === */
        $EXPECTED = 'LV';
        /** === Call and asserts  === */
        $res = $this->obj->getDefaultCountryCode();
        $this->assertEquals($EXPECTED, $res);
    }

    public function test_getReferralCode()
    {
        /** === Test Data === */
        $CODE = 'code';
        /** === Setup Mocks === */
        // $result = $this->_registry->registry(static::REG_REFERRAL_CODE);
        $this->mRegistry
            ->shouldReceive('registry')->once()
            ->andReturn($CODE);
        /** === Call and asserts  === */
        $res = $this->obj->getReferralCode();
        $this->assertEquals($CODE, $res);
    }

    public function test_processCoupon()
    {
        /** === Test Data === */
        $COUPON = 'coupon';
        /** === Setup Mocks === */
        // is not implemented yet
        /** === Call and asserts  === */
        $this->obj->processCoupon($COUPON);
    }

    public function test_processHttpRequest()
    {
        /** === Test Data === */
        $GET_VAR = '654321';
        $COOKIE = '123456:20160520';
        $NOW = '20160520';
        /** === Setup Mocks === */
        $this->obj = \Mockery::mock(
            Referral::class . '[replaceCodeInRegistry]',
            [$this->mCookieManager, $this->mRegistry, $this->mToolDate]
        );
        // $cookie = $this->_cookieManager->getCookie(static::COOKIE_REFERRAL_CODE);
        $this->mCookieManager
            ->shouldReceive('getCookie')->once()
            ->andReturn($COOKIE);
        // $tsSaved = $this->_toolDate->getUtcNow();
        $mTsSaved = $this->_mock(\DateTime::class);
        $this->mToolDate
            ->shouldReceive('getUtcNow')->once()
            ->andReturn($mTsSaved);
        // $saved = $tsSaved->format('Ymd');
        $mTsSaved->shouldReceive('format')->once()
            ->andReturn($NOW);
        // $this->_cookieManager->setPublicCookie(static::COOKIE_REFERRAL_CODE, $cookie, $meta);
        $this->mCookieManager
            ->shouldReceive('setPublicCookie')->once();
        // $this->replaceCodeInRegistry($code);
        $this->obj
            ->shouldReceive('replaceCodeInRegistry')->once();
        /** === Call and asserts  === */
        $this->obj->processHttpRequest($GET_VAR);
    }

    public function test_replaceCodeInRegistry()
    {
        /** === Test Data === */
        $CODE = 'code';
        $FROM_REGISTRY = 'saved';
        /** === Setup Mocks === */
        // if ($this->_registry->registry(static::REG_REFERRAL_CODE)) {
        $this->mRegistry
            ->shouldReceive('registry')->once()
            ->andReturn($FROM_REGISTRY);
        // $this->_registry->unregister(static::REG_REFERRAL_CODE);
        $this->mRegistry
            ->shouldReceive('unregister')->once();
        // $this->_registry->register(static::REG_REFERRAL_CODE, $code);
        $this->mRegistry
            ->shouldReceive('register')->once();
        /** === Call and asserts  === */
        $this->obj->replaceCodeInRegistry($CODE);
    }
}