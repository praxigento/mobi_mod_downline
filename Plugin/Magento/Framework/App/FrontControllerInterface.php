<?php
/**
 * Plugin for \Magento\Framework\App\FrontController
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Magento\Framework\App;

use Praxigento\Downline\Config as Cfg;

class FrontControllerInterface
{
    /** Name of the HTTP GET variable for referral code */
    const REQ_REFERRAL = Cfg::KEY_REF_CODE;
    /** @var \Praxigento\Downline\Api\Helper\Referral */
    private $hlpRefCode;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Praxigento\Downline\Api\Helper\Referral $hlpRefCode
    ) {
        $this->session = $session;
        $this->hlpRefCode = $hlpRefCode;
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
        $code = $request->getParam(static::REQ_REFERRAL);
        if (!empty($code)) {
            $this->session->logout();
        }
        $this->hlpRefCode->processHttpRequest($code);
    }
}