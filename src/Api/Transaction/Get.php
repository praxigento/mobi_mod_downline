<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Transaction;


class Get
    extends \Praxigento\Accounting\Api\Transaction\Get
{
    /** @var  \Praxigento\Downline\Repo\Query\Trans\Get\Builder */
    protected $qbld;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Accounting\Repo\Query\Trans\Get\Builder $qbldTrans,
        \Praxigento\Core\Api\IAuthenticator $authenticator,
        \Praxigento\Downline\Repo\Query\Trans\Get\Builder $qbldDwnlTrans
    ) {
        parent::__construct($manObj, $qbldTrans, $authenticator);
        /* replace parent $qbld by own (bad practice, I know :() */
        $this->qbld = $qbldDwnlTrans;
    }
}