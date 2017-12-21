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
    /** @var \Praxigento\Core\App\Api\Web\IAuthenticator */
    private $authenticator;
    /** @var \Praxigento\Downline\Api\Service\Customer\Get\ById */
    private $servCustGet;

    public function __construct(
        \Praxigento\Core\App\Api\Web\IAuthenticator $authenticator,
        \Praxigento\Downline\Api\Service\Customer\Get\ById $servCustGet
    ) {
        $this->authenticator = $authenticator;
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
        $email = $data->getEmail();
        $mlmId = $data->getMlmId();
        $custId = $data->getCustomerId();

        /* get currently logged in users */
        $currentAdminId = $this->authenticator->getCurrentAdminId($request);
        $currentCustId = $this->authenticator->getCurrentCustomerId($request);

        /* analyze logged in users */
        $isAdminRequest = false;
        $requesterId = null;
        if ($currentCustId) {
            /* this is customer session */
            $requesterId = $currentCustId;
        } elseif ($currentAdminId) {
            $isAdminRequest = true;
        }

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