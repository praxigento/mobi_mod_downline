<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Framework\View\Element\UiComponent\DataProvider;

class CollectionFactory
{
    /** @var  Sub\QueryModifier */
    protected $_subQueryModifier;

    public function __construct(
        Sub\QueryModifier $subQueryModufier
    ) {
        $this->_subQueryModifier = $subQueryModufier;
    }

    /**
     * Modify result collection for "customer_listing_data_source" (add joins & filter mapping, MOBI-335).
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Closure $proceed
     * @param $requestName
     * @return null
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'customer_listing_data_source') {
            if ($result instanceof \Magento\Customer\Model\ResourceModel\Grid\Collection) {
                /* add JOINS to the select query */
                $this->_subQueryModifier->populateSelect($result);
                /* add fields to mapping */
                $this->_subQueryModifier->addFieldsMapping($result);
            }
        }
        return $result;
    }
}