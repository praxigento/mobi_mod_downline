<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

/**
 * Get downline subtree entries for current customer.
 */
interface EntriesInterface
{
    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Entries\Request $req
     * @return \Praxigento\Downline\Api\Tree\Get\Entries\Response
     */
    public function execute(\Praxigento\Downline\Api\Tree\Get\Entries\Request $req);
}