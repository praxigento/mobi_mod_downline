<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree;

use Praxigento\Downline\Config as Cfg;

class Get
    implements \Praxigento\Downline\Api\Tree\GetInterface
{
    const BIND_MAX_DEPTH = 'maxDepth';
    const BIND_ON_DATE = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::BIND_DATE;
    const BIND_PATH = 'path';
    const BIND_ROOT_CUSTOMER_ID = 'rootCustId';

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
    /** @var \Praxigento\Core\Tool\IPeriod */
    protected $toolPeriod;
    public function __construct(
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoCustomer,
        \Praxigento\Downline\Repo\Entity\ISnap $repoSnap,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Actual\Builder $qbuildSnapActual,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp\Builder $qbuildSnapDcp,
        \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder $qbuildSnapOnDate
    ) {
        $this->toolPeriod = $toolPeriod;
        $this->repoCustomer = $repoCustomer;
        $this->repoSnap = $repoSnap;
        $this->qbuildSnapActual = $qbuildSnapActual;
        $this->qbuildSnapDcp = $qbuildSnapDcp;
        $this->qbuildSnapOnDate = $qbuildSnapOnDate;
    }

    public function exec(\Praxigento\Downline\Api\Tree\Get\Request $data)
    {
        $result = new \Praxigento\Downline\Api\Tree\Get\Response();
        if ($data->getRequestReturn()) {
            $result->setRequest($data);
        }
        /* parse request, prepare query and fetch data */
        $bind = $this->prepareQueryParameters($data);
        $query = $this->getSelectQuery($bind);
        $query = $this->populateQuery($query, $bind);
        $rs = $this->performQuery($query, $bind);
        $rsData = new \Flancer32\Lib\Data($rs);
        $result->setData($rsData->get());
        return $result;
    }

    /**
     *
     * @param \Praxigento\Downline\Api\Tree\Get\Request $data
     * @return \Praxigento\Downline\Api\Tree\Get\Response
     */
    public function execute(\Praxigento\Downline\Api\Tree\Get\Request $data)
    {
        $result = new \Praxigento\Downline\Api\Tree\Get\Response();
        if ($data->getRequestReturn()) {
            $result->setRequest($data);
        }
        /* extract request attributes */
        $maxDepth = $data->getMaxDepth();
        $onDate = $data->getOnDate();
        $rootNode = $data->getRootCustId();
        /* compose query according to given conditions */
        $bind = [];
        if (is_null($onDate)) {
            /* if $period is missed use 'prxgt_dwnl_customer' as base for query */
            $baseQbuild = $this->qbuildSnapActual;
            if (!is_null($rootNode)) {
                /* get root customer from actual data */
                $customerRoot = $this->repoCustomer->getById($rootNode);
            }
        } else {
            /* else - use 'prxgt_dwnl_snap' as base for query*/
            $baseQbuild = $this->qbuildSnapOnDate;
            $bind[\Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::BIND_DATE] = $onDate;
            if (!is_null($rootNode)) {
                /* get root customer from snaps */
                $customerRoot = $this->repoSnap->getByCustomerIdOnDate($rootNode, $onDate);
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
            $customerEmail = $row[\Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp\Builder::AS_ATTR_EMAIL];
            $customerId = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_CUSTOMER_ID];
            $customerMlmId = $row[\Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp\Builder::AS_ATTR_MLM_ID];
            $nameFirst = $row[\Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp\Builder::AS_ATTR_NAME_FIRST];
            $nameLast = $row[\Praxigento\Downline\Repo\Query\Snap\OnDate\ForDcp\Builder::AS_ATTR_NAME_LAST];
            $customerName = "$nameFirst $nameLast";
            $depthInTree = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_DEPTH];
            $parentId = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_PARENT_ID];
            $path = $row[\Praxigento\Downline\Data\Entity\Snap::ATTR_PATH];
            $entry = new \Praxigento\Downline\Api\Data\Tree\Node();
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
        $responseData = new \Praxigento\Downline\Api\Tree\Get\Response\Data();
        $responseData->setNodes($entries);
        $result->setData($responseData);
        $result->getResult()->setCode($result::CODE_SUCCESS);
        return $result;
    }

    /**
     * Analyze query parameters and return query to select data with filters/orders/limits.
     *
     * @param \Flancer32\Lib\Data $bind
     * @return \Praxigento\Core\Repo\Query\IBuilder
     */
    protected function getSelectQuery(\Flancer32\Lib\Data $bind = null)
    {
        $onDate = $bind->get(self::BIND_ON_DATE);
        if (is_null($onDate)) {
            /* if $period is missed use 'prxgt_dwnl_customer' as base for query */
            $baseQbuild = $this->qbuildSnapActual;
        } else {
            /* else - use 'prxgt_dwnl_snap' as base for query*/
            $baseQbuild = $this->qbuildSnapOnDate;
        }
        $query = $this->qbuildSnapDcp->getSelectQuery($baseQbuild);
        return $query;
    }

    protected function performQuery($query, \Flancer32\Lib\Data $bind)
    {
        $conn = $query->getConnection();
        $bind->unset(self::BIND_ROOT_CUSTOMER_ID);
        $rs = $conn->fetchAll($query, (array)$bind->get());
        return $rs;
    }

    protected function populateQuery(
        \Magento\Framework\DB\Select $query,
        \Flancer32\Lib\Data $bind
    ) {
        /* get important query parameters */
        $onDate = $bind->get(self::BIND_ON_DATE);
        $rootCustId = $bind->get(self::BIND_ROOT_CUSTOMER_ID);
        $maxDepth = $bind->get(self::BIND_MAX_DEPTH);
        if (is_null($onDate)) {
            /* if $onDate is missed use 'prxgt_dwnl_customer' as base for query */
            if (!is_null($rootCustId)) {
                /* get root customer from actual data */
                $customerRoot = $this->repoCustomer->getById($rootCustId);
            }
        } else {
            /* else - use 'prxgt_dwnl_snap' as base for query*/
            if (!is_null($rootCustId)) {
                /* get root customer from snaps */
                $customerRoot = $this->repoSnap->getByCustomerIdOnDate($rootCustId, $onDate);
            }
        }
        /* if $rootCustId is defined - get customer/snap data for parent's path */
        if (!is_null($rootCustId)) {
            // customerRoot should be loaded before
            $idRoot = $customerRoot->getCustomerId();
            $pathRoot = $customerRoot->getPath();
            $where = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_TBL_DWNL_SNAP . '.' .
                \Praxigento\Downline\Data\Entity\Snap::ATTR_PATH . ' LIKE :' . self::BIND_PATH;
            $bind->set(self::BIND_PATH, $pathRoot . $idRoot . Cfg::DTPS . '%');
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
                \Praxigento\Downline\Data\Entity\Snap::ATTR_DEPTH . ' < :' . self::BIND_MAX_DEPTH;
            $bind->set(self::BIND_MAX_DEPTH, $filterDepth);
            $query->where($where);
        }
        return $query;
    }

    /**
     * @param \Flancer32\Lib\Data $data
     * @return array
     */
    protected function prepareQueryParameters(\Flancer32\Lib\Data $data)
    {
        $result = new \Flancer32\Lib\Data();
        /** @var \Praxigento\Downline\Api\Tree\Get\Request $data */
        $maxDepth = $data->getMaxDepth();
        $onDate = $data->getOnDate();
        $rootCustId = $data->getRootCustId();
        if (is_null($rootCustId)) {
            $user = $this->authenticator->getCurrentUserData();
            $rootCustId = $user->get(Cfg::E_CUSTOMER_A_ENTITY_ID);
        }
        $result->set(self::BIND_ROOT_CUSTOMER_ID, $rootCustId);
        if ($maxDepth) $result->set(self::BIND_MAX_DEPTH, $maxDepth);
        if ($onDate) {
            /* convert YYYYMM to YYYYMMDD */
            $lastDate = $this->toolPeriod->getPeriodLastDate($onDate);
            $result->set(self::BIND_ON_DATE, $lastDate);
        }
        return $result;
    }
}