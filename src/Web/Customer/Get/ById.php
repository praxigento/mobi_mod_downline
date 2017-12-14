<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Web\Customer\Get;

use Praxigento\Downline\Api\Web\Customer\Get\ById\Request as ARequest;
use Praxigento\Downline\Api\Web\Customer\Get\ById\Response as AResponse;

class ById
    implements \Praxigento\Downline\Api\Web\Customer\Get\ByIdInterface
{
    /** @var \Praxigento\Downline\Api\Service\Customer\Get\ById */
    private $servCustGet;

    public function __construct(
        \Praxigento\Downline\Api\Service\Customer\Get\ById $servCustGet
    ) {
        $this->servCustGet = $servCustGet;
    }

    /**
     * @param ARequest $request
     * @return AResponse|void
     */
    public function exec($request) {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $dev = $request->getDev();
        $email = $data->getEmail();
        $mlmId = $data->getMlmId();
        $custId = $data->getCustomerId();

        /* TODO: add request authorization */
        $isAdminRequest = true;
        $requesterId = $custId;

        /** perform processing */
        $req = new \Praxigento\Downline\Api\Service\Customer\Get\ById\Request();
        $req->setCustomerId($custId);
        $req->setEmail($email);
        $req->setMlmId($mlmId);
        $req->setIgnoreRequester($isAdminRequest);
        $req->setRequesterId($requesterId);
        $resp = $this->servCustGet->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }
}