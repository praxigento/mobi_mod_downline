<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Tool\Def;

use Praxigento\Downline\Data\Value\ReferralCookie;
use Praxigento\Downline\Tool\IReferralCode;

class ReferralCode implements IReferralCode
{
    /** Cookie name to save referral code and creation date into browser */
    const COOKIE_REFERRAL_CODE = 'prxgtDwnlReferral';
    /** Key in registry to save referral code */
    const REG_REFERRAL_CODE = 'prxgtDwnlReferral';
    /** @var \Magento\Framework\Stdlib\CookieManagerInterface */
    protected $_cookieManager;
    protected $_registry;
    /** @var \Magento\Framework\Session\SessionManagerInterface */
    protected $_sessionManager;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Praxigento\Core\Tool\IDate $toolDate
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_registry = $registry;
        $this->_sessionManager = $sessionManager;
        $this->_toolDate = $toolDate;
    }

    public function getCode()
    {
        $result = $this->_registry->registry(static::REG_REFERRAL_CODE);
        return $result;
    }

    public function processCoupon($coupon)
    {
        // TODO: Implement processCoupon() method.
    }

    public function processHttpRequest($getVar)
    {
        /* get code from cookie */
        $cookie = $this->_cookieManager->getCookie(static::COOKIE_REFERRAL_CODE);
        $voCookie = new ReferralCookie($cookie);
        /* replace cookie value if GET code is not equal to cookie value */
        if ($getVar != $voCookie->getCode()) {
            $voCookie->setCode($getVar);
            $cookie = $voCookie->generateCookieValue();
            $this->_cookieManager->setSensitiveCookie(static::COOKIE_REFERRAL_CODE, $cookie);
            /* save referral code into the registry */
            $code = $voCookie->getCode();
            $this->replaceCodeInRegistry($code);
        }
    }

    public function replaceCodeInRegistry($code)
    {
        if ($this->_registry->registry(static::REG_REFERRAL_CODE)) {
            $this->_registry->unregister(static::REG_REFERRAL_CODE);
        }
        $this->_registry->register(static::REG_REFERRAL_CODE, $code);
    }
}