<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Controller\Customer;

use Praxigento\Downline\Api\Ctrl\Customer\Search\Request as ARequest;
use Praxigento\Downline\Api\Ctrl\Customer\Search\Response as AResponse;

/**
 * Web API action to search customer by key (name, email, MLM ID).
 */
class Search
    extends \Praxigento\Core\App\Action\Front\Api\Base
{
    /** @var \Praxigento\Core\Api\Service\Customer\Search */
    private $callSearch;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Core\App\Web\IAuthenticator $authenticator,
        \Praxigento\Core\Api\Service\Customer\Search $callSearch
    )
    {
        parent::__construct($context, $inputProcessor, $outputProcessor, $logger, $authenticator);
        $this->callSearch = $callSearch;
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
        /* define local working data */
        assert($request instanceof \Praxigento\Downline\Api\Service\Customer\Search\Request);
        $customerId = $request->getCustomerId();

        /* perform processing */
        $customerId = $this->authenticator->getCurrentCustomerId($customerId);
        $request->setCustomerId($customerId);
        $result = $this->callSearch->exec($request);

        /* compose result */
        return $result;
    }


}