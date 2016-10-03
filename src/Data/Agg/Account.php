<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Data\Agg;


class Account
    extends \Praxigento\Accounting\Data\Agg\Account
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_REF = 'Ref';
    /**#@- */

    /** @return string */
    public function getReference()
    {
        $result = parent::getData(self::AS_REF);
        return $result;
    }

    public function setReference($data)
    {
        parent::setData(self::AS_REF, $data);
    }

}