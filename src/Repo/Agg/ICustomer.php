<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg;

use Praxigento\Core\Repo\IAggregate;
use Praxigento\Downline\Data\Agg\Customer as AggCustomer;

interface IWarehouse extends IAggregate
{
    const AS_DWNL_CUST = 'pdc';
    const AS_MAGE_CUST = 'cgf';

    /**
     * @param array|AggCustomer $data
     * @return AggCustomer
     */
    public function create($data);

    /**
     * @param int $id
     * @return AggCustomer|null
     */
    public function getById($id);
}