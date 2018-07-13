<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Web\Customer\Search;

use Praxigento\Downline\Api\Web\Customer\Search\ByKey\Request as ARequest;
use Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response as AResponse;

class ByKey
    implements \Praxigento\Downline\Api\Web\Customer\Search\ByKeyInterface
{
    /** @var \Praxigento\Core\Api\App\Web\Authenticator */
    private $authenticator;
    /** @var \Praxigento\Downline\Api\Service\Customer\Search */
    private $servCustSearch;

    public function __construct(
        \Praxigento\Core\Api\App\Web\Authenticator\Front $authenticator,
        \Praxigento\Downline\Api\Service\Customer\Search $servCustSearch
    ) {
        $this->authenticator = $authenticator;
        $this->servCustSearch = $servCustSearch;
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $limit = $data->getLimit();
        $key = $data->getSearchKey();

        /* pre-authorization: deny anonymous visitors */
        $currentCustId = $this->authenticator->getCurrentUserId($request);
        if (!$currentCustId) {
            $phrase = new \Magento\Framework\Phrase('Anonymous user is not authorized to perform this operation.');
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Magento\Framework\Exception\AuthorizationException($phrase);
        }

        /** perform processing */
        $req = new \Praxigento\Downline\Api\Service\Customer\Search\Request();
        $req->setCustomerId($currentCustId);
        $req->setLimit($limit);
        $req->setSearchKey($key);
        $resp = $this->servCustSearch->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }
}