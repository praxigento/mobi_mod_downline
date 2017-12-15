<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Helper;

/**
 * Helper for referrals related functionality.
 */
interface Referral
{
    /** Cookie name to save referral code and creation date into browser */
    const COOKIE_REFERRAL_CODE = 'prxgtDwnlReferral';
    /** Key in registry to save referral code */
    const REG_REFERRAL_CODE = 'prxgtDwnlReferral';

    /**
     * Get 2-chars uppercase code for the default country for Downline Tree (LV, LT, EE, ...).
     * @return string
     */
    public function getDefaultCountryCode();

    /**
     * Get referral code saved in registry.
     *
     * @return string
     */
    public function getReferralCode();

    /**
     * Analyze checkout coupon and save referral code into the registry.
     *
     * @param string $coupon
     */
    public function processCoupon($coupon);

    /**
     * Analyze GET variable, compare with current cookie and save referral code into registry.
     *
     * @param string $codeGetVar
     */
    public function processHttpRequest($codeGetVar);

    /**
     * Replace referral code in registry.
     *
     * @param $code
     * @return mixed
     */
    public function replaceCodeInRegistry($code);

    /**
     * Set cookie with referral code (should be called in the end of request processing, MOBI-1022).
     * @param string $code referral code
     */
    public function setCookie($code);
}