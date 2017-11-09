<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp;

use Praxigento\Downline\Config as Cfg;

/**
 * Build query to get downline tree snap on given date with additional attributes for DCP.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Builder
{
    /** Tables aliases. */
    const AS_CUSTOMER = 'mageCust';
    const AS_DOWNLINE_CUSTOMER = 'prxgtDwnlCust';
    /** Columns aliases. */
    const A_COUNTRY_CODE = 'country';
    const A_EMAIL = 'email';
    const A_MLM_ID = 'mlmId';
    const A_NAME_FIRST = 'nameFirst';
    const A_NAME_LAST = 'nameLast';
    const A_NAME_MIDDLE = 'nameMiddle';

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = clone $source;
        $asDwnlCust = self::AS_DOWNLINE_CUSTOMER;
        $asDwnlSnap = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_DWNL_SNAP;
        $asCust = self::AS_CUSTOMER;
        /* LEFT JOIN prxgt_dwnl_customer pdc */
        $tblDwnlCust = [
            $asDwnlCust => $this->resource->getTableName(\Praxigento\Downline\Repo\Entity\Data\Customer::ENTITY_NAME)
        ];
        $on = $asDwnlCust . '.' . \Praxigento\Downline\Repo\Entity\Data\Customer::ATTR_CUSTOMER_ID . '='
            . $asDwnlSnap . '.' . \Praxigento\Downline\Repo\Entity\Data\Snap::ATTR_CUSTOMER_ID;
        $cols = [
            self::A_MLM_ID => \Praxigento\Downline\Repo\Entity\Data\Customer::ATTR_HUMAN_REF,
            self::A_COUNTRY_CODE => \Praxigento\Downline\Repo\Entity\Data\Customer::ATTR_COUNTRY_CODE
        ];
        $result->joinLeft($tblDwnlCust, $on, $cols);
        /* LEFT JOIN customer_entity ce */
        $tblCust = [
            $asCust => $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER)
        ];
        $on = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '='
            . $asDwnlCust . '.' . \Praxigento\Downline\Repo\Entity\Data\Customer::ATTR_CUSTOMER_ID;
        $cols = [
            self::A_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_NAME_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_NAME_MIDDLE => Cfg::E_CUSTOMER_A_MIDDLENAME,
            self::A_NAME_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $result->joinLeft($tblCust, $on, $cols);
        return $result;
    }

}