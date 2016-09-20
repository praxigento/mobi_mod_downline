<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg\Def\Account;

use Praxigento\Downline\Data\Agg\Account as Agg;
use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Repo\Agg\IAccount as Repo;

class Mapper
    extends \Praxigento\Accounting\Repo\Agg\Def\Account\Mapper
{

    public function __construct()
    {
        parent::__construct();
        $this->_map[Agg::AS_REF] = Repo::AS_DOWNLINE . '.' . Customer::ATTR_HUMAN_REF;
    }

}