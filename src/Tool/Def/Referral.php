<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Tool\Def;

use Praxigento\Downline\Data\Value\ReferralCookie;
use Praxigento\Downline\Tool\IReferral;

class Referral implements IReferral
{
    /** Cookie name to save referral code and creation date into browser */
    const COOKIE_REFERRAL_CODE = 'prxgtDwnlReferral';
    /** Key in registry to save referral code */
    const REG_REFERRAL_CODE = 'prxgtDwnlReferral';
    /** @var \Magento\Framework\Stdlib\CookieManagerInterface */
    protected $_cookieManager;
    /** @var \Magento\Framework\Registry */
    protected $_registry;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Registry $registry,
        \Praxigento\Core\Tool\IDate $toolDate
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_registry = $registry;
        $this->_toolDate = $toolDate;
    }

    public function getDefaultCountryCode()
    {
        return 'LV';
    }

    /** @inheritdoc */
    public function getReferralCode()
    {
        $result = $this->_registry->registry(static::REG_REFERRAL_CODE);
        return $result;
    }

    /** @inheritdoc */
    public function processCoupon($coupon)
    {
        // TODO: Implement processCoupon() method.
    }

    /** @inheritdoc */
    public function processHttpRequest($getVar)
    {
        /* get code from cookie */
        $cookie = $this->_cookieManager->getCookie(static::COOKIE_REFERRAL_CODE);
        $voCookie = new ReferralCookie($cookie);
        /* replace cookie value if GET code is not equal to cookie value */
        if (
            $getVar &&
            ($getVar != $voCookie->getCode())
        ) {
            $tsSaved = $this->_toolDate->getUtcNow();
            $saved = $tsSaved->format('Ymd');
            $voCookie->setCode($getVar);
            $voCookie->setDateSaved($saved);
            $cookie = $voCookie->generateCookieValue();
            $meta = new \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata();
            $meta->setPath('/');
            $meta->setDurationOneYear();
            $this->_cookieManager->setPublicCookie(static::COOKIE_REFERRAL_CODE, $cookie, $meta);
        }
        /* save referral code into the registry */
        $code = $voCookie->getCode();
        $this->replaceCodeInRegistry($code);
    }

    public function replaceCodeInRegistry($code)
    {
        if ($this->_registry->registry(static::REG_REFERRAL_CODE)) {
            $this->_registry->unregister(static::REG_REFERRAL_CODE);
        }
        $this->_registry->register(static::REG_REFERRAL_CODE, $code);
    }
}