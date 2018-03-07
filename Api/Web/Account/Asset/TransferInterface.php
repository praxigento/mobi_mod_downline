<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Account\Asset;

/**
 * Process asset transfer between accounts with downline restrictions.
 */
interface TransferInterface
    extends \Praxigento\Accounting\Api\Web\Account\Asset\TransferInterface
{
    /**
     * @param \Praxigento\Downline\Api\Web\Account\Asset\Transfer\Request $request
     * @return \Praxigento\Downline\Api\Web\Account\Asset\Transfer\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}