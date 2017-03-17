<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Transaction;


class Get
    extends \Praxigento\Accounting\Api\Transaction\Get
{
    /** @var  \Praxigento\Downline\Repo\Query\Trans\Get\Builder */
    protected $qbldDwnlTrans;

    public function __construct(
        \Praxigento\Core\Api\IAuthenticator $authenticator,
        \Praxigento\Accounting\Repo\Query\Trans\Get\Builder $qbldTrans,
        \Praxigento\Downline\Repo\Query\Trans\Get\Builder $qbldDwnlTrans
    ) {
        parent::__construct($authenticator, $qbldTrans);
        $this->qbldDwnlTrans = $qbldDwnlTrans;
    }

    /**
     * Don't use original query builder. Replace with own one.
     *
     * @param \Flancer32\Lib\Data $ctx
     */
    protected function getSelectQuery(\Flancer32\Lib\Data $ctx)
    {
        $query = $this->qbldDwnlTrans->getSelectQuery();
        $ctx->set(self::CTX_QUERY, $query);
    }
}