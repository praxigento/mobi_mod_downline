<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

/**
 * Get downline subtree entries for the customer (including bonus stats).
 *
 * @deprecated TODO: use it or remove it.
 */
interface EntriesInterface
{
    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Entries\Request $data
     * @return \Praxigento\Downline\Api\Tree\Get\Entries\Response
     */
    public function execute(\Praxigento\Downline\Api\Tree\Get\Entries\Request $data);
}