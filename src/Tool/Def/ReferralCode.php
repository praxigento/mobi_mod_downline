<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Tool\Def;

use Praxigento\Downline\Data\Value\ReferralCookie;
use Praxigento\Downline\Tool\IReferralCode;

class ReferralCode implements IReferralCode
{
    /** Cookie to save referral code and creation date into browser */
    const COOKIE_REFERRAL_CODE = 'prxgtDwnlReferral';
    /** @var \Magento\Framework\Stdlib\CookieManagerInterface */
    protected $_cookieManager;
    /** @var \Magento\Framework\Session\SessionManagerInterface */
    protected $_sessionManager;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Praxigento\Core\Tool\IDate $toolDate
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_sessionManager = $sessionManager;
        $this->_toolDate = $toolDate;
    }

    public function processCoupon($coupon)
    {
        // TODO: Implement processCoupon() method.
    }

    public function processHttpRequest($getVar)
    {
        $cookie = $this->_cookieManager->getCookie(static::COOKIE_REFERRAL_CODE);
        $voCookie = new ReferralCookie($cookie);
        $value = $this->_sessionManager->getSomeValue();
        $this->_sessionManager->setSomeValue('bubuka' . $value);
    }
}