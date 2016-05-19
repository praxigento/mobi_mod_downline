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
    /** @var \Praxigento\Downline\Tool\IReferralCode */
    protected $_toolReferralCode;

    public function __construct(
        \Praxigento\Downline\Tool\IReferralCode $toolReferralCode
    ) {
        $this->_toolReferralCode = $toolReferralCode;
    }

    public function beforeDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $reqCode = $request->getParam(Cfg::REQ_REFERRAL);
        $this->_toolReferralCode->processHttpRequest($reqCode);
        return;
    }
}