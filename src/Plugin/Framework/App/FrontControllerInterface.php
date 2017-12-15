<?php
/**
 * Plugin for \Magento\Framework\App\FrontController
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Framework\App;


class FrontControllerInterface
{
    /** Name of the HTTP GET variable for referral code */
    const REQ_REFERRAL = 'prxgtDwnlReferral';
    /** @var \Praxigento\Downline\Api\Helper\Referral */
    protected $hlpRefCode;

    public function __construct(
        \Praxigento\Downline\Api\Helper\Referral $hlpRefCode
    )
    {
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
    )
    {
        $code = $request->getParam(static::REQ_REFERRAL);
        $this->hlpRefCode->processHttpRequest($code);
    }
}