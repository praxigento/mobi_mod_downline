<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Transaction;


class Get
    extends \Praxigento\Accounting\Api\Transaction\Get
{

    protected function getQueryBuilder()
    {
        $result = $this->manObj->get(\Praxigento\Downline\Repo\Query\Trans\Get\Builder::class);
        return $result;
    }

}