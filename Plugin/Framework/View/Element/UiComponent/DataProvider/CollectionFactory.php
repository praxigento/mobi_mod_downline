<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Plugin\Framework\View\Element\UiComponent\DataProvider;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Plugin\Framework\View\Element\UiComponent\DataProvider\CollectionFactory\A\QueryModifier\Customers as AQModCustomers;
use Praxigento\Downline\Plugin\Framework\View\Element\UiComponent\DataProvider\CollectionFactory\A\QueryModifier\Sales as AQModSales;

class CollectionFactory
{
    /** @var  AQModCustomers */
    private $aQModCustomers;
    /** @var AQModSales */
    private $aQModSales;

    public function __construct(
        AQModCustomers $aQModCustomers,
        AQModSales $aQModSales
    ) {
        $this->aQModCustomers = $aQModCustomers;
        $this->aQModSales = $aQModSales;
    }

    /**
     * Modify result collection for grids data sources (add joins & filter mapping).
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
        if ($requestName == Cfg::DS_CUSTOMERS_GRID) {
            if ($result instanceof \Magento\Customer\Model\ResourceModel\Grid\Collection) {
                /* add JOINS to the select query */
                $this->aQModCustomers->populateSelect($result);
                /* add fields to mapping */
                $this->aQModCustomers->addFieldsMapping($result);
            }
        } elseif ($requestName == Cfg::DS_SALES_ORDERS_GRID) {
            if ($result instanceof \Magento\Sales\Model\ResourceModel\Order\Grid\Collection) {
                /* add JOINS to the select query */
                $this->aQModSales->populateSelect($result);
                /* add fields to mapping */
                $this->aQModSales->addFieldsMapping($result);
            }
        }
        return $result;
    }
}