<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get\Entries\Response;

class Data
    extends \Flancer32\Lib\Data
{
    /**
     * @return \Praxigento\Downline\Api\Data\Tree\Node[]|null
     */
    public function getEntries()
    {
        $result = parent::getEntries();
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Data\Tree\Node[] $data
     */
    public function setEntries($data)
    {
        parent::setEntries($data);
    }
}