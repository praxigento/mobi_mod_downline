<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Add additional attributes to the Customer Grid in adminhtml.
 *
 * @package Praxigento\Downline\Observer
 */
class CoreCollectionAbstractLoadBefore implements ObserverInterface
{

    /** @var  \Praxigento\Downline\Repo\Partial\ICustomerGrid */
    protected $_repoPartCustomergrid;

    public function __construct(
        \Praxigento\Downline\Repo\Partial\ICustomerGrid $repoPartCustomerGrid
    ) {
        $this->_repoPartCustomergrid = $repoPartCustomerGrid;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getData('collection');
        if ($collection instanceof \Magento\Customer\Model\ResourceModel\Grid\Collection) {
            $query = $collection->getSelect();
            $this->_repoPartCustomergrid->populateSelect($query);
        }
        return;
    }
}