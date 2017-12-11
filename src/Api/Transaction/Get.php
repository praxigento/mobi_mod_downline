<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Transaction;

/**
 * Extends Accounting API operation.
 *
 * TODO: we need to re-design API extension or remove this variant.
 */
class Get
    extends \Praxigento\Accounting\Api\Rest\Transaction\Get
{
    /** @var  \Praxigento\Downline\Repo\Query\Trans\Get */
    protected $qbld;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Accounting\Repo\Query\Trans\Get\Builder $qbldTrans,
        \Praxigento\Core\Helper\Config $hlpCfg,
        \Praxigento\Core\App\Api\Web\IAuthenticator $authenticator,
        \Praxigento\Downline\Repo\Query\Trans\Get $qbldDwnlTrans
    )
    {
        parent::__construct($manObj, $qbldTrans, $hlpCfg, $authenticator);
        /* replace parent $qbld by own (bad practice, I know :() */
        $this->qbld = $qbldDwnlTrans;
    }
}