<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline;

class Config extends \Praxigento\Core\Config
{
    /** Downline Tree path separator */
    const DTPS = '/';
    /**Depth for root nodes. */
    const INIT_DEPTH = 0;
    const MODULE = 'Praxigento_Downline';
    /** Name of the HTTP GET variable for referral code */
    const REQ_REFERRAL = 'prxgtDwnlReferral';
}