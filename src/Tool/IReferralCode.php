<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Tool;

/**
 * Referral Code related operations.
 */
interface IReferralCode
{
    public function getCode();

    public function processCoupon($coupon);

    public function processHttpRequest($getVar);
}