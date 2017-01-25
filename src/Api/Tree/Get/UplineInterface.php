<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

/**
 * Get upline data for current customer.
 */
interface UplineInterface
{
    /**
     * @param \Praxigento\Downline\Api\Tree\Get\Upline\Request $data
     * @return \Praxigento\Downline\Api\Tree\Get\Upline\Response
     */
    public function execute(\Praxigento\Downline\Api\Tree\Get\Upline\Request $data);
}