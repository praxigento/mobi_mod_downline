<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

class Upline
    implements \Praxigento\Downline\Api\Tree\Get\UplineInterface
{

    public function execute(\Praxigento\Downline\Api\Tree\Get\Upline\Request $data)
    {
        return true;
    }
}