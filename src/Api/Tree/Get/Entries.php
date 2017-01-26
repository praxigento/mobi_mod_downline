<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

class Entries
    implements \Praxigento\Downline\Api\Tree\Get\EntriesInterface
{
    /** @var \Praxigento\Downline\Service\ISnap */
    protected $callSnap;

    public function __construct(
        \Praxigento\Downline\Service\ISnap $callSnap
    ) {
        $this->callSnap = $callSnap;
    }

    /**
     * MOBI-592: additional commit
     *
     * @param \Praxigento\Downline\Api\Tree\Get\Entries\Request $data
     * @return \Praxigento\Downline\Api\Tree\Get\Entries\Response
     */
    public function execute(\Praxigento\Downline\Api\Tree\Get\Entries\Request $data)
    {
        $result = new \Praxigento\Downline\Api\Tree\Get\Entries\Response();
        if ($data->getRequestReturn()) {
            $result->setRequest($data);
        }
        $maxDepth = $data->getMaxDepth();
        $maxEntries = $data->getMaxEntries();
        $period = $data->getPeriod();
        $rootNode = $data->getRootNode();
        $requestReturn = $data->getRequestReturn();
        $reqCall = new \Praxigento\Downline\Service\Snap\Request\GetStateOnDate();
        $reqCall->setDatestamp($period);
        $reqCall->setRootId($rootNode);
        $resp = $this->callSnap->getStateOnDate($reqCall);
        $rows = $resp->get();
        $entries = [];
        foreach ($rows as $row) {
            $countryCode = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_CUSTOMER_ID];
            $customerEmail = $row[\Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDateForDcp::AS_ATTR_EMAIL];
            $customerId = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_CUSTOMER_ID];
            $customerMlmId = $row[\Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDateForDcp::AS_ATTR_MLM_ID];
            $nameFirst = $row[\Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDateForDcp::AS_ATTR_NAME_FIRST];
            $nameLast = $row[\Praxigento\Downline\Repo\Entity\Def\Snap\Query\OnDateForDcp::AS_ATTR_NAME_FIRST];
            $customerName = "$nameFirst $nameLast";
            $depthInTree = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_DEPTH];
            $parentId = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_PARENT_ID];
            $path = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_PATH];
            $entry = new \Praxigento\Downline\Api\Data\Tree\Entry();
            $entry->setCountryCode($countryCode);
            $entry->setCustomerEmail($customerEmail);
            $entry->setCustomerId($customerId);
            $entry->setCustomerMlmId($customerMlmId);
            $entry->setCustomerName($customerName);
            $entry->setDepthInTree($depthInTree);
            $entry->setParentId($parentId);
            $entry->setPath($path);
            $entries[$customerId] = $entry;
        }
        $responseData = new \Praxigento\Downline\Api\Tree\Get\Entries\Response\Data();
        $responseData->setEntries($entries);
        $result->setData($responseData);
        $result->getResult()->setCode($result::CODE_SUCCESS);
        return $result;
    }
}