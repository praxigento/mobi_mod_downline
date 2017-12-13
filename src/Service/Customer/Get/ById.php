<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Get;

use Praxigento\Downline\Api\Service\Customer\Get\ById\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Get\ById\Response as AResponse;
use Praxigento\Downline\Repo\Query\Customer\Get as QBGetCustomer;

class ById
    implements \Praxigento\Downline\Api\Service\Customer\Get\ById
{
    private $qbCustGet;

    public function __construct(
        \Praxigento\Downline\Repo\Query\Customer\Get $qbCustGet
    )
    {
        $this->qbCustGet = $qbCustGet;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        /* define local working data */
        assert($request instanceof ARequest);
        $customerId = $request->getCustomerId();

        /* perform processing */
        $result = $this->loadCustomerData($customerId);

        /* compose result */
        return $result;
    }

    private function loadCustomerData($custId)
    {
        $query = $this->qbCustGet->build();
        $conn = $query->getConnection();
        $bind = [
            QBGetCustomer::BND_CUST_ID => $custId
        ];
        $rs = $conn->fetchRow($query, $bind);

        /* extract DB data */
        $custId = $rs[QBGetCustomer::A_ID];
        $email = $rs[QBGetCustomer::A_EMAIL];
        $nameFirst = $rs[QBGetCustomer::A_NAME_FIRST];
        $nameLast = $rs[QBGetCustomer::A_NAME_LAST];
        $mlmId = $rs[QBGetCustomer::A_MLM_ID];

        /* compose API data */
        $result = new AResponse();
        $result->setId($custId);
        $result->setEmail($email);
        $result->setNameFirst($nameFirst);
        $result->setNameLast($nameLast);
        $result->setMlmId($mlmId);

        return $result;
    }
}