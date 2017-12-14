<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Get;

use Praxigento\Core\Config as Cfg;
use Praxigento\Downline\Api\Service\Customer\Get\ById\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Get\ById\Response as AResponse;
use Praxigento\Downline\Repo\Entity\Data\Customer as EDwnlCust;
use Praxigento\Downline\Repo\Query\Customer\Get as QBGetCustomer;

class ById
    implements \Praxigento\Downline\Api\Service\Customer\Get\ById
{
    /** @var \Praxigento\Downline\Repo\Query\Customer\Get */
    private $qbCustGet;

    public function __construct(
        \Praxigento\Downline\Repo\Query\Customer\Get $qbCustGet
    ) {
        $this->qbCustGet = $qbCustGet;
    }

    /**
     * Convert database query result set to response object.
     * @param array $db
     * @return \Praxigento\Downline\Api\Service\Customer\Get\ById\Response
     */
    private function convertDbToApi($db) {
        $result = new AResponse();
        if ($db) {
            /* extract DB data */
            $custId = $db[QBGetCustomer::A_ID];
            $email = $db[QBGetCustomer::A_EMAIL];
            $nameFirst = $db[QBGetCustomer::A_NAME_FIRST];
            $nameLast = $db[QBGetCustomer::A_NAME_LAST];
            $mlmId = $db[QBGetCustomer::A_MLM_ID];

            /* compose response data */
            $result->setId($custId);
            $result->setEmail($email);
            $result->setNameFirst($nameFirst);
            $result->setNameLast($nameLast);
            $result->setMlmId($mlmId);
        }
        return $result;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request) {
        /** define local working data */
        assert($request instanceof ARequest);
        $customerId = $request->getCustomerId();
        $email = $request->getEmail();
        $ignoreRequester = $request->getIgnoreRequester();
        $mlmId = $request->getMlmId();
        $requesterId = $request->getRequesterId();

        /** perform processing */
        /* TODO: add search by email for frontend requests (when customerId is not set) */
        if (
            $ignoreRequester ||
            ($customerId == $requesterId)
        ) {
            /* process if this is admin request or customer requests info for itself */
            if ($customerId) {
                /* customer ID has a higher priority */
                $result = $this->searchById($customerId);
            } elseif ($email) {
                /* ... then search by email */
                $result = $this->searchByEmail($email);
            } elseif ($mlmId) {
                /* ... then search by MLM ID */
                $result = $this->searchByMlmId($email);
            } else {
                $result = new AResponse(); // empty result
            }
        }

        /** compose result */
        return $result;
    }

    private function searchByEmail($email) {
        $query = $this->qbCustGet->build();
        $conn = $query->getConnection();
        /* reset WHERE part and recreate new one */
        $query->reset(\Zend_Db_Select::WHERE);
        $bnd = 'email';
        $where = QBGetCustomer::AS_MAGE_CUST . '.' . Cfg::E_CUSTOMER_A_EMAIL . '=:' . $bnd;
        $query->where($where);
        /* prepare vars to bind */
        $bind = [
            $bnd => $email
        ];
        $rs = $conn->fetchRow($query, $bind);
        $result = $this->convertDbToApi($rs);
        return $result;
    }

    private function searchById($custId) {
        $query = $this->qbCustGet->build();
        $conn = $query->getConnection();
        $bind = [
            QBGetCustomer::BND_CUST_ID => (int)$custId
        ];
        $rs = $conn->fetchRow($query, $bind);
        $result = $this->convertDbToApi($rs);
        return $result;
    }

    private function searchByMlmId($mlmId) {
        $query = $this->qbCustGet->build();
        $conn = $query->getConnection();
        /* reset WHERE part and recreate new one */
        $query->reset(\Zend_Db_Select::WHERE);
        $bnd = 'mlmId';
        $where = QBGetCustomer::E_DWNL_CUST . '.' . EDwnlCust::ATTR_MLM_ID . '=:' . $bnd;
        $query->where($where);
        /* prepare vars to bind */
        $bind = [
            $bnd => $mlmId
        ];
        $rs = $conn->fetchRow($query, $bind);
        $result = $this->convertDbToApi($rs);
        return $result;
    }
}