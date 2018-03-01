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
    const DTPS = ':';
    /**Depth for root nodes. */
    const INIT_DEPTH = 0;
    const MODULE = 'Praxigento_Downline';
    const ROUTE_NAME_ADMIN = 'prxgt_dwnl';
    /** Name for GET parameter with referral code value */
    const KEY_REF_CODE = 'ref';
}