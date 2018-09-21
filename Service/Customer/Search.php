<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 *
 * @noinspection PhpDocMissingThrowsInspection
 */

namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\Search\Response as AResponse;
use Praxigento\Downline\Api\Service\Customer\Search\Response\Item as DItem;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Downline\Repo\Query\Customer\Get as QBGetCustomer;

/**
 * Get suggestions for customers by key (name/email/mlm_id).
 */
class Search
    implements \Praxigento\Downline\Api\Service\Customer\Search
{
    /** Default limit for result set items. */
    const DEF_LIMIT = 10;

    /** @var \Praxigento\Downline\Repo\Query\Customer\Get */
    private $qGetCustomer;

    public function __construct(
        \Praxigento\Downline\Repo\Query\Customer\Get $qGetCustomer
    ) {
        $this->qGetCustomer = $qGetCustomer;
    }

    /**
     * Convert database query result set to response object.
     *
     * @param array $db
     * @return DItem
     * @throws \Exception
     */
    private function convertDbToApi($db)
    {
        $result = new DItem();
        if ($db) {
            /* extract DB data */
            $custId = $db[QBGetCustomer::A_ID];
            $nameFirst = $db[QBGetCustomer::A_NAME_FIRST];
            $nameLast = $db[QBGetCustomer::A_NAME_LAST];
            $mlmId = $db[QBGetCustomer::A_MLM_ID];
            $path = $db[QBGetCustomer::A_PATH];
            $country = $db[QBGetCustomer::A_COUNTRY];

            /* prepare response data */
            $pathFull = $path . $custId . Cfg::DTPS;

            /* compose response data (w/o email, see MOBI-1678)*/
            $result->setId($custId);
            $result->setNameFirst($nameFirst);
            $result->setNameLast($nameLast);
            $result->setMlmId($mlmId);
            $result->setCountry($country);
            $result->setPathFull($pathFull);
        }
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Service\Customer\Search\Request $req
     * @return \Praxigento\Downline\Api\Service\Customer\Search\Response
     * @throws \Exception
     */
    public function exec($req)
    {
        /** define local working data */
        $rootCustId = $req->getCustomerId();
        $key = $req->getSearchKey();
        $limit = $req->getLimit() ?? self::DEF_LIMIT;

        /** perform processing */
        $items = $this->selectCustomers($key, $limit);

        /** compose result */
        $result = new AResponse();
        $result->setItems($items);
        return $result;
    }

    /**
     * @param string $key
     * @param int $limit
     * @return array
     */
    private function selectCustomers($key, $limit)
    {
        $query = $this->qGetCustomer->build();
        $conn = $query->getConnection();
        $searchBy = $conn->quote("%$key%");
        /* reset all WHERE clauses */
        $query->reset(\Zend_Db_Select::WHERE);
        /* add WHERE clause */
        $asCust = QBGetCustomer::AS_MAGE_CUST;
        $asDwnl = QBGetCustomer::AS_DWNL_CUST;
        $byFirst = "$asCust." . Cfg::E_CUSTOMER_A_FIRSTNAME . " LIKE $searchBy";
        $byLast = "$asCust." . Cfg::E_CUSTOMER_A_LASTNAME . " LIKE $searchBy";
        $byMlmID = "$asDwnl." . EDwnlCust::A_MLM_ID . " LIKE $searchBy";
        $where = "($byFirst) OR ($byLast) OR ($byMlmID)";

        $query->where($where);
        /* add LIMIT clause */
        $query->limit($limit);

        /* perform query & convert DB data to API data */
        $rows = $conn->fetchAll($query);
        $result = [];
        foreach ($rows as $row) {
            $item = $this->convertDbToApi($row);
            $result[] = $item;
        }
        return $result;
    }
}