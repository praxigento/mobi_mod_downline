<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

/**
 * Plug for the operation.
 */
class Entries
    implements \Praxigento\Downline\Api\Tree\Get\EntriesInterface
{
    /**
     *
     * @param \Praxigento\Downline\Api\Tree\Get\Entries\Request $data
     * @return \Praxigento\Downline\Api\Tree\Get\Entries\Response
     */
    public function execute(\Praxigento\Downline\Api\Tree\Get\Entries\Request $data)
    {
        $result = new \Praxigento\Downline\Api\Tree\Get\Entries\Response();
        $entries = [];
        $responseData = new \Praxigento\Downline\Api\Tree\Get\Entries\Response\Data();
        $responseData->setEntries($entries);
        $result->setData($responseData);
        $result->getResult()->setCode($result::CODE_NOT_IMPLEMENTED);
        return $result;
    }
}