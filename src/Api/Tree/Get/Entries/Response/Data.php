<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get\Entries\Response;

class Data
    extends \Flancer32\Lib\Data
{
    /**
     * @return \Praxigento\Downline\Api\Data\Tree\Entry[]|null
     */
    public function getEntries()
    {
        $result = parent::getEntries();
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Data\Tree\Entry[] $data
     */
    public function setEntries($data)
    {
        parent::setEntries($data);
    }
}