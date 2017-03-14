<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Transaction;


class Get
    extends \Praxigento\Accounting\Api\Transaction\Get
{
    public function exec(\Praxigento\Accounting\Api\Transaction\Get\Request $data)
    {
        $result = new \Praxigento\Downline\Api\Transaction\Get\Response();
        $sub = parent::exec($data);
        $result->setData($sub->getData());
        return $result;
    }

    protected function getQueryBuilder()
    {
        $result = $this->manObj->get(\Praxigento\Downline\Repo\Query\Trans\Get\Builder::class);
        return $result;
    }

}