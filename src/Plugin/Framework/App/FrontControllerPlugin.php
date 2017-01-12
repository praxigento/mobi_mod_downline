<?php
/**
 * Plugin for \Magento\Framework\App\FrontController
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Framework\App;

use Magento\Framework\App\FrontController as FC;


class FrontControllerPlugin
{
    /** Name of the HTTP GET variable for referral code */
    const REQ_REFERRAL = 'prxgtDwnlReferral';
    /** @var \Praxigento\Downline\Tool\IReferral */
    protected $toolReferralCode;

    public function __construct(
        \Praxigento\Downline\Tool\IReferral $toolReferralCode
    ) {
        $this->toolReferralCode = $toolReferralCode;
    }

    /**
     * Extract referral code from GET-variable or cookie and save it into registry.
     *
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function beforeDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $reqCode = $request->getParam(static::REQ_REFERRAL);
        $this->toolReferralCode->processHttpRequest($reqCode);
    }
}