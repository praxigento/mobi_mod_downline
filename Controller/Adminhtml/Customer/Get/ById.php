<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Controller\Adminhtml\Customer\Get;

use Praxigento\Downline\Api\Web\Customer\Get\ById\Request as ARequest;
use Praxigento\Downline\Api\Web\Customer\Get\ById\Response as AResponse;

class ById
    extends \Praxigento\Core\App\Action\Back\Api\Base
{

    /** @var \Praxigento\Downline\Api\Service\Customer\Get\ById */
    private $servCustGet;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Downline\Api\Service\Customer\Get\ById $servCustGet
    ) {
        parent::__construct($context);
        $this->servCustGet = $servCustGet;
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
        $email = $data->getEmail();
        $mlmId = $data->getMlmId();
        $custId = $data->getCustomerId();

        /** perform processing */
        $req = new \Praxigento\Downline\Api\Service\Customer\Get\ById\Request();
        $req->setCustomerId($custId);
        $req->setEmail($email);
        $req->setMlmId($mlmId);
        $resp = $this->servCustGet->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        /* conveyors are bad for debug */
        $result->getResult()->setCode(AResponse::CODE_SUCCESS);
        return $result;
    }
}