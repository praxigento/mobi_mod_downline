<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Controller\Adminhtml\Customer\Search;

use Praxigento\Downline\Api\Web\Customer\Search\ByKey\Request as ARequest;
use Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response as AResponse;

class ByKey
    extends \Praxigento\Core\App\Action\Back\Api\Base
{

    /** @var \Praxigento\Downline\Api\Service\Customer\Search */
    private $servCustSearch;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Downline\Api\Service\Customer\Search $servCustSearch
    ) {
        parent::__construct($context);
        $this->servCustSearch = $servCustSearch;
    }

    protected function getInDataType(): string
    {
        return ARequest::class;
    }

    protected function getOutDataType(): string
    {
        return AResponse::class;
    }

    protected function process($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $limit = $data->getLimit();
        $key = $data->getSearchKey();

        /** perform processing */
        $req = new \Praxigento\Downline\Api\Service\Customer\Search\Request();
        $req->setSearchKey($key);
        $req->setLimit($limit);
        $resp = $this->servCustSearch->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        $result->getResult()->setCode(AResponse::CODE_SUCCESS);
        return $result;
    }
}