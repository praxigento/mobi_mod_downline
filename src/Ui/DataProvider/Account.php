<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Ui\DataProvider;

/**
 * Data provider for "Accounting / Accounts" grid.
 */
class Account
    extends \Praxigento\Core\Ui\DataProvider\Base
{

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Praxigento\Core\Repo\Query\Criteria\IAdapter $criteriaAdapter,
        \Praxigento\Downline\Repo\Agg\Def\Account\Mapper $api2sqlMapper,
        \Praxigento\Downline\Repo\Agg\IAccount $repo,
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        $name,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $url,
            $criteriaAdapter,
            $api2sqlMapper,
            $repo,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $name,
            $meta,
            $data
        );
    }

}