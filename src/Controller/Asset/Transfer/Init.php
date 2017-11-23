<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Controller\Asset\Transfer;

use Magento\Framework\Controller\ResultFactory as AResultFactory;

class Init
    extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(AResultFactory::TYPE_JSON);

        $data = ['var' => 'value'];
        $resultPage->setData($data);
        return $resultPage;
    }

}