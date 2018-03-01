<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Controller\Reflink;

class Index
    extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $type = \Magento\Framework\Controller\ResultFactory::TYPE_PAGE;
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create($type);
        $resultPage->getConfig()->getTitle()->set(__('Referral Link'));
        return $resultPage;
    }

}