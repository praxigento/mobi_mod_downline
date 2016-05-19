<?php
/**
 * Plugin for \Magento\Framework\App\FrontController
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Framework\App;

use Magento\Framework\App\FrontController as FC;
use Praxigento\Downline\Config as Cfg;


class FrontControllerPlugin
{
    /** @var \Praxigento\Downline\Tool\IReferral */
    protected $_toolReferralCode;

    public function __construct(
        \Praxigento\Downline\Tool\IReferral $toolReferralCode
    ) {
        $this->_toolReferralCode = $toolReferralCode;
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
        $reqCode = $request->getParam(Cfg::REQ_REFERRAL);
        $this->_toolReferralCode->processHttpRequest($reqCode);
    }
}