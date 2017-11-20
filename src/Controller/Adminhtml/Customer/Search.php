<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Customer\Accounting;

/**
 * Get suggestions for customers by key (name/email/mlm_id).
 */
class Search
    extends \Magento\Backend\App\Action
{
    const VAR_LIMIT = 'limit';
    const VAR_SEARCH_KEY = 'search_key';

    /** @var \Praxigento\Downline\Api\Customer\SearchInterface */
    private $callSearch;
    /** @var \Magento\Framework\Webapi\ServiceOutputProcessor */
    private $outputProcessor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Downline\Api\Customer\SearchInterface $callSearch
    )
    {
        parent::__construct($context);
        $this->outputProcessor = $outputProcessor;
        $this->callSearch = $callSearch;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $searchKey = $this->getRequest()->getParam(self::VAR_SEARCH_KEY);
        $limit = $this->getRequest()->getParam(self::VAR_LIMIT);

        /* TODO: add ACL */
        $userId = $this->_auth->getUser()->getId();
        $req = new \Praxigento\Downline\Api\Customer\Search\Request();
        $req->setSearchKey($searchKey);
        $req->setLimit($limit);
        $resp = $this->callSearch->exec($req);

        /* convert service data object into JSON */
        $className = \Praxigento\Downline\Api\Customer\SearchInterface::class;
        $methodName = 'exec';
        $stdResp = $this->outputProcessor->process($resp, $className, $methodName);

        /* extract data part from response */
        $data = $stdResp[\Praxigento\Core\Api\Response::ATTR_DATA];
        $resultPage->setData($data);
        return $resultPage;
    }
}