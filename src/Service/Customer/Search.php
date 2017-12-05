<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\Search\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Search\Response as AResponse;
use Praxigento\Downline\Api\Service\Customer\Search\Response\Data as DRespData;
use Praxigento\Downline\Api\Service\Customer\Search\Response\Data\Item as DItem;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Entity\Data\Customer as EDwnlCust;
use Praxigento\Downline\Repo\Query\Customer\Get as QBGetCustomer;

/**
 * Get suggestions for customers by key (name/email/mlm_id).
 */
class Search
    implements \Praxigento\Downline\Api\Service\Customer\Search
{
    const DEF_LIMIT = 10;

    /** @var \Praxigento\Downline\Repo\Query\Customer\Get */
    private $qbGetCustomer;

    public function __construct(
        \Praxigento\Downline\Repo\Query\Customer\Get $qbGetCustomer
    )
    {
        $this->qbGetCustomer = $qbGetCustomer;
    }

    /**
     * @param ARequest $req
     * @return AResponse
     */
    public function exec($req)
    {
        /* define local working data */
        $key = $req->getSearchKey();
        $limit = $req->getLimit() ?? self::DEF_LIMIT;

        /* perform processing */
        $items = $this->selectCustomers($key, $limit);

        /* compose result */
        $result = new AResponse();
        $data = new DRespData();
        $data->setItems($items);
        $result->setData($data);
        return $result;
    }

    private function selectCustomers($key, $limit)
    {
        $query = $this->qbGetCustomer->build();
        $conn = $query->getConnection();
        $searchBy = $conn->quote("%$key%");
        /* reset all WHERE clauses */
        $query->reset(\Zend_Db_Select::WHERE);
        /* add WHERE clause */
        $asCust = QBGetCustomer::AS_MAGE_CUST;
        $asDwnl = QBGetCustomer::AS_DWNL_CUST;
        $byFirst = "$asCust." . Cfg::E_CUSTOMER_A_FIRSTNAME . " LIKE $searchBy";
        $byLast = "$asCust." . Cfg::E_CUSTOMER_A_LASTNAME . " LIKE $searchBy";
        $byEmail = "$asCust." . Cfg::E_CUSTOMER_A_EMAIL . " LIKE $searchBy";
        $byMlmID = "$asDwnl." . EDwnlCust::ATTR_MLM_ID . " LIKE $searchBy";
        $where = "($byFirst) OR ($byLast) OR ($byEmail) OR ($byMlmID)";
        $query->where($where);
        /* add LIMIT clause */
        $query->limit($limit);

        /* perform query & convert DB data to API data */
        $rows = $conn->fetchAll($query);
        $result = [];
        foreach ($rows as $row) {
            /* parse DB data */
            $id = $row[QBGetCustomer::A_ID];
            $nameFirst = $row[QBGetCustomer::A_NAME_FIRST];
            $nameLast = $row[QBGetCustomer::A_NAME_LAST];
            $email = $row[QBGetCustomer::A_EMAIL];
            $mlmId = $row[QBGetCustomer::A_MLM_ID];

            /* compose API data */
            $item = new DItem();
            $item->setId($id);
            $item->setNameFirst($nameFirst);
            $item->setNameLast($nameLast);
            $item->setEmail($email);
            $item->setMlmId($mlmId);
            $result[] = $item;
        }
        return $result;
    }
}