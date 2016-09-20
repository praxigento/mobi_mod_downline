<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Agg;

use Praxigento\Downline\Data\Agg\Account as Agg;

interface IAccount
    extends \Praxigento\Accounting\Repo\Agg\IAccount
{
    const AS_DOWNLINE = 'pdc';

    /**
     * @param array|Agg $data
     * @return Agg
     */
    public function create($data);

    /**
     * @param int $id
     * @return Agg|null
     */
    public function getById($id);
}