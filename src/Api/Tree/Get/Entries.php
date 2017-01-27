<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

use Praxigento\Downline\Config as Cfg;

class Entries
    implements \Praxigento\Downline\Api\Tree\Get\EntriesInterface
{
    /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\Actual\Builder */
    protected $qbuildSnapActual;
    /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp\Builder */
    protected $qbuildSnapDcp;
    /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder */
    protected $qbuildSnapOnDate;
    /** @var \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $repoCustomer;
    /** @var \Praxigento\Downline\Repo\Entity\ISnap */
    protected $repoSnap;

    public function __construct(
        \Praxigento\Downline\Repo\Entity\ICustomer $repoCustomer,
        \Praxigento\Downline\Repo\Entity\ISnap $repoSnap,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Actual\Builder $qbuildSnapActual,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp\Builder $qbuildSnapDcp,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder $qbuildSnapOnDate
    ) {
        $this->repoCustomer = $repoCustomer;
        $this->repoSnap = $repoSnap;
        $this->qbuildSnapActual = $qbuildSnapActual;
        $this->qbuildSnapDcp = $qbuildSnapDcp;
        $this->qbuildSnapOnDate = $qbuildSnapOnDate;
    }

    /**
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
        /* extract request attributes */
        $maxDepth = $data->getMaxDepth();
        $period = $data->getPeriod();
        $rootNode = $data->getRootNode();
        /* compose query according to given conditions */
        $bind = [];
        if (is_null($period)) {
            /* if $period is missed use 'prxgt_dwnl_customer' as base for query */
            $baseQbuild = $this->qbuildSnapActual;
            if (!is_null($rootNode)) {
                /* get root customer from actual data */
                $customerRoot = $this->repoCustomer->getById($rootNode);
            }
        } else {
            /* else - use 'prxgt_dwnl_snap' as base for query*/
            $baseQbuild = $this->qbuildSnapOnDate;
            $bind[\Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::BIND_DATE] = $period;
            if (!is_null($rootNode)) {
                /* get root customer from snaps */
                $customerRoot = $this->repoSnap->getByCustomerIdOnDate($rootNode, $period);
            }
        }
        /* additionally setup query */
        $query = $this->qbuildSnapDcp->getSelectQuery($baseQbuild);
        /* if $rootNode is defined - get customer/snap data for parent's path */
        if (!is_null($rootNode)) {
            // customerRoot should be loaded before
            $idRoot = $customerRoot->getCustomerId();
            $pathRoot = $customerRoot->getPath();
            $where = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_TBL_DWNL_SNAP . '.' .
                \Praxigento\Downline\Data\Entity\Snap::ATTR_PATH . ' LIKE :path';
            $bind['path'] = $pathRoot . $idRoot . Cfg::DTPS . '%';
            $query->where($where);
        }
        if (!is_null($maxDepth)) {
            if (isset($customerRoot)) {
                /* depth started from 0, add +1 to strat from root */
                $filterDepth = $customerRoot->getDepth() + 1 + $maxDepth;
            } else {
                $filterDepth = $maxDepth;
            }
            $where = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_TBL_DWNL_SNAP . '.' .
                \Praxigento\Downline\Data\Entity\Snap::ATTR_DEPTH . ' < :depth';
            $bind['depth'] = (int)$filterDepth;
            $query->where($where);
        }
        /* perform query and re-pack results for API */
        $conn = $this->qbuildSnapDcp->getConnection();
        $rows = $conn->fetchAll($query, $bind);
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