<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Framework\App;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class FrontControllerPlugin_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mToolReferralCode;
    /** @var  FrontControllerPlugin */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mToolReferralCode = $this->_mock(\Praxigento\Downline\Tool\IReferral::class);
        /** create object to test */
        $this->obj = new FrontControllerPlugin(
            $this->mToolReferralCode
        );
    }

    public function test_beforeDispatch()
    {
        /** === Test Data === */
        $CODE = 'GET code';
        /** === Setup Mocks === */
        $mSubject = $this->_mock(\Magento\Framework\App\FrontControllerInterface::class);
        $mRequest = $this->_mock(\Magento\Framework\App\RequestInterface::class);
        // $reqCode = $request->getParam(static::REQ_REFERRAL);
        $mRequest->shouldReceive('getParam')->once()
            ->andReturn($CODE);
        // $this->_toolReferralCode->processHttpRequest($reqCode);
        $this->mToolReferralCode
            ->shouldReceive('processHttpRequest')->once();
        /** === Call and asserts  === */
        $this->obj->beforeDispatch($mSubject, $mRequest);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(FrontControllerPlugin::class, $this->obj);
    }
}