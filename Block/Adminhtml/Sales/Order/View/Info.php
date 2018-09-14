<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Block\Adminhtml\Sales\Order\View;

/**
 * Add MLM ID to sale order view form.
 */
class Info
    extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;

    public function __construct(
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->daoDwnlCust = $daoDwnlCust;
    }

    public function getMlmId()
    {
        $result = '';
        $sale = $this->getOrder();
        $custId = $sale->getCustomerId();
        if ($custId) {
            $found = $this->daoDwnlCust->getById($custId);
            if ($found) {
                $result = $found->getMlmId();
            }
        }
        return $result;
    }
}