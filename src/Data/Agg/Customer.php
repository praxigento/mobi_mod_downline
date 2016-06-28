<?php
/**
 * Aggregate for Magneto Customer data.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Data\Agg;

use Flancer32\Lib\DataObject;

class Customer extends DataObject
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_PARENT_ID = 'ParentId';
    /**#@- */

    public function getParentId()
    {
        $result = parent::getData(self::AS_PARENT_ID);
        return $result;
    }

    public function setParentId($data)
    {
        parent::setData(self::AS_PARENT_ID, $data);
    }

}