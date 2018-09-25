<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Magento\Customer\Controller\Account;

use Magento\Framework\Controller\ResultFactory as AResultFactory;

class CreatePost
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
    }

    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function aroundExecute()
    {
//        $result = $proceed();
        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create(AResultFactory::TYPE_RAW);
        $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_UNAUTHORIZED);
        $result->setContents('Please, use referral link to register as customer.');
        return $result;
    }
}