<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree;

/**
 * Get downline subtree.
 *
 * @deprecated TODO: use it or remove it.
 */
interface GetInterface
{
    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Request $data
     * @return \Praxigento\Downline\Api\Tree\Get\Response
     */
    public function exec(\Praxigento\Downline\Api\Tree\Get\Request $data);
}