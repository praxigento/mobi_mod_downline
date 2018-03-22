<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Web\Customer\Get;

use Praxigento\Downline\Api\Service\Customer\Get\ById\Response as ARespData;
use Praxigento\Downline\Api\Web\Customer\Get\ById\Request as ARequest;
use Praxigento\Downline\Api\Web\Customer\Get\ById\Response as AResponse;

/**
 * Web service for customers to get customer data by identificator.
 */
class ById
    implements \Praxigento\Downline\Api\Web\Customer\Get\ByIdInterface
{
    /** @var \Praxigento\Core\Api\App\Web\Authenticator */
    private $authenticator;
    /** @var \Praxigento\Downline\Helper\Downline */
    private $hlpDwnl;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $repoCust;
    /** @var \Praxigento\Downline\Api\Service\Customer\Get\ById */
    private $servCustGet;

    public function __construct(
        \Praxigento\Core\Api\App\Web\Authenticator\Front $authenticator,
        \Praxigento\Downline\Repo\Dao\Customer $repoCust,
        \Praxigento\Downline\Helper\Downline $hlpDwnl,
        \Praxigento\Downline\Api\Service\Customer\Get\ById $servCustGet
    ) {
        $this->authenticator = $authenticator;
        $this->repoCust = $repoCust;
        $this->hlpDwnl = $hlpDwnl;
        $this->servCustGet = $servCustGet;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     * @throws \Magento\Framework\Exception\AuthorizationException
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $email = $data->getEmail();
        $mlmId = $data->getMlmId();
        $custId = $data->getCustomerId();

        /* pre-authorization: deny anonymous visitors */
        $currentCustId = $this->authenticator->getCurrentUserId($request);
        if (!$currentCustId) {
            $phrase = new \Magento\Framework\Phrase('User is not authorized to perform this operation.');
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Magento\Framework\Exception\AuthorizationException($phrase);
        }

        /** perform processing */
        $req = new \Praxigento\Downline\Api\Service\Customer\Get\ById\Request();
        $req->setCustomerId($custId);
        $req->setEmail($email);
        $req->setMlmId($mlmId);
        $resp = $this->servCustGet->exec($req);

        /* post-authorization: customer can access his own data or his own downline customer */
        $foundCustId = $resp->getId();
        $foundCustData = $this->repoCust->getById($foundCustId);
        $path = $foundCustData->getPath();
        $parents = $this->hlpDwnl->getParentsFromPath($path);
        if (
            ($currentCustId != $foundCustId) &&
            !in_array($currentCustId, $parents)
        ) {
            /* reset result if found customer is not requester itself & not in requester's downline */
            /* don't throw exception - it will a way to check identificator existence */
            $resp = new ARespData();
        }

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }
}