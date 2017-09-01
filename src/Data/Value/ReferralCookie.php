<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Data\Value;

use Praxigento\Core\Data as DataObject;

/**
 * Value Object for the referral cookie ('123456:20160529').
 *
 * @method string getCode() Referral code.
 * @method void setCode(string $code)
 * @method string getDateSaved() Date stamp for the cookie saving time.
 * @method void setDateSaved(string $code)
 */
class ReferralCookie extends DataObject
{
    /** Separator for the parts in the cookie's value */
    const VS = ':';

    public function __construct($cookieValue)
    {
        $this->parseCookieValue($cookieValue);
    }

    public function generateCookieValue()
    {
        $result = $this->getCode() . static::VS . $this->getDateSaved();
        return $result;
    }

    public function parseCookieValue($value)
    {
        $parts = explode(static::VS, $value);
        if (count($parts) >= 2) {
            $this->setCode($parts[0]);
            $this->setDateSaved($parts[1]);
        }

    }
}