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
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var \Magento\Framework\Stdlib\CookieManagerInterface */
    protected $manCookie;
    /** @var \Magento\Framework\Registry */
    protected $registry;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Registry $registry,
        \Praxigento\Core\Tool\IDate $toolDate
    )
    {
        $this->logger = $logger;
        $this->manCookie = $cookieManager;
        $this->registry = $registry;
        $this->toolDate = $toolDate;
    }

    public function getDefaultCountryCode()
    {
        return 'LV';
    }

    public function getReferralCode()
    {
        $result = $this->registry->registry(static::REG_REFERRAL_CODE);
        return $result;
    }

    public function processCoupon($coupon)
    {
        $this->replaceCodeInRegistry($coupon);
    }

    public function processHttpRequest($codeGetVar)
    {
        /* extract code from cookie (if exists) */
        $cookie = $this->manCookie->getCookie(static::COOKIE_REFERRAL_CODE);
        $voCookie = new ReferralCookie($cookie);
        $code = $voCookie->getCode();
        /* replace cookie value if GET code is not equal to cookie value */
        if (
            $codeGetVar &&
            ($codeGetVar != $code)
        ) {
            $code = $codeGetVar;
        }
        /* save referral code into the registry */
        if ($code) {
            $this->replaceCodeInRegistry($code);
        }
    }

    public function replaceCodeInRegistry($code)
    {
        if ($this->registry->registry(static::REG_REFERRAL_CODE)) {
            $this->registry->unregister(static::REG_REFERRAL_CODE);
        }
        $this->registry->register(static::REG_REFERRAL_CODE, $code);
    }

    public function setCookie($code)
    {
        $tsSaved = $this->toolDate->getUtcNow();
        $saved = $tsSaved->format('Ymd');
        $voCookie = new ReferralCookie('');
        $voCookie->setCode($code);
        $voCookie->setDateSaved($saved);
        $cookie = $voCookie->generateCookieValue();
        $meta = new \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata();
        $meta->setPath('/');
        $meta->setDurationOneYear();
        $this->manCookie->setPublicCookie(static::COOKIE_REFERRAL_CODE, $cookie, $meta);
    }
}