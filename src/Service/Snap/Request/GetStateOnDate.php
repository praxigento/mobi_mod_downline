<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Snap\Request;

/**
 * @method int getRootId()
 * @method void setRootId(int $data)
 */
class GetStateOnDate
    extends \Praxigento\Core\Service\Base\Request
{
    /** 'true' - add actual country code for customer's attributes  */
    const ADD_COUNTRY_CODE = 'add_country_code';
    /**
     * @var string 'YYYYMMDD'
     */
    const DATE_STAMP = 'datestamp';

    /**
     * @return bool 'true' - add actual country code for customer's attributes
     */
    public function getAddCountryCode()
    {
        $result = $this->get(self::ADD_COUNTRY_CODE);
        return $result;
    }

    public function getDatestamp()
    {
        $result = $this->get(self::DATE_STAMP);
        return $result;
    }

    /**
     * 'true' - add actual country code for customer's attributes
     *
     * @param bool $data
     */
    public function setAddCountryCode($data)
    {
        $this->set(self::ADD_COUNTRY_CODE, $data);
    }

    public function setDatestamp($data)
    {
        $this->set(self::DATE_STAMP, $data);
    }
}