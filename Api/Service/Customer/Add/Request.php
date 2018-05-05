<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Service\Customer\Add;

/**
 * @method string getCountryCode()
 * @method void setCountryCode(string $data)
 * @method int getCustomerId()
 * @method void setCustomerId(int $data)
 * @method string getDate()
 * @method void setDate(string $data)
 * @method string getMlmId()
 * @method void setMlmId(string $data)
 * @method int getParentId()
 * @method void setParentId(int $data)
 * @method string getReferralCode()
 * @method void setReferralCode(string $data)
 */
class Request
    extends \Praxigento\Core\App\Service\Request
{
    const COUNTRY_CODE = 'countryCode';
    const CUSTOMER_ID = 'customerId';
    const DATE = 'date';
    const MLM_ID = 'mlmId';
    const PARENT_ID = 'parentId';
    const REFERRAL_CODE = 'referralCode';

}