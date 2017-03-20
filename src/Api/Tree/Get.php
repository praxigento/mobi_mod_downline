<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree;

use Praxigento\Downline\Config as Cfg;

class Get
    extends \Praxigento\Core\Api\Processor\WithQuery
    implements \Praxigento\Downline\Api\Tree\GetInterface
{
    const BIND_MAX_DEPTH = 'maxDepth';
    const BIND_ON_DATE = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::BIND_ON_DATE;
    const BIND_PATH = 'path';
    const BIND_ROOT_CUSTOMER_ID = 'rootCustId';

    const VAR_CUST_DEPTH = 'depth';
    const VAR_CUST_ID = 'cust_id';
    const VAR_CUST_PATH = 'path';
    const VAR_MAX_DEPTH = 'max_depth';
    const VAR_ON_DATE = 'on_date';

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

    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Request $data
     * @return \Praxigento\Downline\Api\Tree\Get\Response
     */
    public function exec(\Praxigento\Downline\Api\Tree\Get\Request $data)
    {
        $result = parent::process($data);
        return $result;
    }

    protected function getSelectQuery(\Flancer32\Lib\Data $ctx)
    {
        /* get working vars from context */
        /** @var \Flancer32\Lib\Data $bind */
        $bind = $ctx->get(self::CTX_BIND);
        /** @var \Flancer32\Lib\Data $vars */
        $vars = $ctx->get(self::CTX_VARS);

        /* analyze variables and compose query according to given conditions */
        $onDate = $vars->get(self::VAR_ON_DATE);
        if (is_null($onDate)) {
            /* if $period is missed use 'prxgt_dwnl_customer' as base for query */
            $baseQbuild = $this->qbuildSnapActual;
        } else {
            /* else - use 'prxgt_dwnl_snap' as base for query*/
            $baseQbuild = $this->qbuildSnapOnDate;
            $bind->set(self::BIND_ON_DATE, $onDate);
        }
        $query = $this->qbuildSnapDcp->getSelectQuery($baseQbuild);

        /* save query to context */
        $ctx->set(self::CTX_QUERY, $query);
    }

    protected function populateQuery(\Flancer32\Lib\Data $ctx)
    {
        /* get working vars from context */
        /** @var \Flancer32\Lib\Data $bind */
        $bind = $ctx->get(self::CTX_BIND);
        /** @var \Flancer32\Lib\Data $vars */
        $vars = $ctx->get(self::CTX_VARS);
        /** @var \Magento\Framework\DB\Select $query */
        $query = $ctx->get(self::CTX_QUERY);

        /* get important query parameters */
        $onDate = $vars->get(self::VAR_ON_DATE);
        $rootCustId = $vars->get(self::VAR_CUST_ID);
        $rootCustDepth = $vars->get(self::VAR_CUST_DEPTH);
        $rootCustPath = $vars->get(self::VAR_CUST_PATH);
        $maxDepth = $vars->get(self::VAR_MAX_DEPTH);

        /* add binding if historical data is requested */
        if (!is_null($onDate)) {
            $bind->set(self::BIND_ON_DATE, $onDate);
        }

        /* filter snap data by root customer path */
        if (!is_null($rootCustId)) {
            $where = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_DWNL_SNAP . '.' .
                \Praxigento\Downline\Data\Entity\Snap::ATTR_PATH . ' LIKE :' . self::BIND_PATH;
            $path = $rootCustPath . $rootCustId . Cfg::DTPS . '%';
            $bind->set(self::BIND_PATH, $path);
            $query->where($where);
        }

        /* filter snap data by max depth */
        if (!is_null($maxDepth)) {
            /* depth started from 0, add +1 to start from root */
            $depth = $rootCustDepth + 1 + $maxDepth;
            $where = \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::AS_DWNL_SNAP . '.' .
                \Praxigento\Downline\Data\Entity\Snap::ATTR_DEPTH . ' < :' . self::BIND_MAX_DEPTH;
            $bind->set(self::BIND_MAX_DEPTH, $depth);
            $query->where($where);
        }
    }

    protected function prepareQueryParameters(\Flancer32\Lib\Data $ctx)
    {
        /* get working vars from context */
        /** @var \Flancer32\Lib\Data $vars */
        $vars = $ctx->get(self::CTX_VARS);
        /** @var \Praxigento\Downline\Api\Tree\Get\Request $req */
        $req = $ctx->get(self::CTX_REQ);

        /* extract request parameters */
        $maxDepth = $req->getMaxDepth();
        $onDate = $req->getOnDate();
        $rootCustId = $req->getRootCustId();

        /* max depth in tree */
        $vars->set(self::VAR_MAX_DEPTH, $maxDepth);

        /* on date */
        if ($onDate) {
            /* convert YYYYMM to YYYYMMDD */
            $lastDate = $this->toolPeriod->getPeriodLastDate($onDate);
            $vars->set(self::VAR_ON_DATE, $lastDate);
        }

        /* root customer */
        if (is_null($rootCustId)) {
            $user = $this->authenticator->getCurrentUserData();
            $rootCustId = $user->get(Cfg::E_CUSTOMER_A_ENTITY_ID);
        }
        if (is_null($onDate)) {
            /* if $onDate is missed use 'prxgt_dwnl_customer' as base for query */
            /* get root customer from actual data */
            $customerRoot = $this->repoCustomer->getById($rootCustId);
        } else {
            /* else - use 'prxgt_dwnl_snap' as base for query*/
            /* get root customer from snaps */
            $customerRoot = $this->repoSnap->getByCustomerIdOnDate($rootCustId, $onDate);
        }
        $depth = $customerRoot->getDepth();
        $path = $customerRoot->getPath();

        /* save working variables into execution context */
        $vars->set(self::VAR_CUST_ID, $rootCustId);
        $vars->set(self::VAR_CUST_DEPTH, $depth);
        $vars->set(self::VAR_CUST_PATH, $path);
    }
}