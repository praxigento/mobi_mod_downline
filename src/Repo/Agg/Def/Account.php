<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg\Def;

class Account
    extends \Praxigento\Accounting\Repo\Agg\Def\Account
    implements \Praxigento\Downline\Repo\Agg\IAccount
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        Account\SelectFactory $factorySelect
    ) {
        parent::__construct($resource, $factorySelect);
    }

}