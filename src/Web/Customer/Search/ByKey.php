<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Web\Customer\Search;

use Praxigento\Downline\Api\Web\Customer\Search\ByKey\Request as ARequest;
use Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response as AResponse;

class ByKey
    implements \Praxigento\Core\Api\Web\Customer\Search\ByKeyInterface
{
    /** @var \Praxigento\Core\App\Api\Web\IAuthenticator */
    private $authenticator;
    /** @var \Praxigento\Downline\Api\Service\Customer\Search */
    private $servCustSearch;

    public function __construct(
        \Praxigento\Core\App\Api\Web\Authenticator\Front $authenticator,
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
            $phrase = new \Magento\Framework\Phrase('User is not authorized to perform this operation.');
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Magento\Framework\Exception\AuthorizationException($phrase);
        }

        /** perform processing */
        $req = new \Praxigento\Downline\Api\Service\Customer\Search\Request();
        $req->setCustomerId($currentCustId);
        $req->setLimit($limit);
        $req->setSearchKey($key);
        $resp = $this->servCustSearch->exec($req);

        /* TODO: post-authorization: customer can access his own data or his own downline customer */
        /** @var \Praxigento\Downline\Api\Service\Customer\Search\Response\Item $item */
//        foreach ($items as $item) {
//            $item->getId();
//        }

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }
}