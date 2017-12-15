<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Helper\Referral;

/**
 * Referral code generator should be overridden in project's application module.
 */
interface CodeGenerator
{
    /**
     * @param \Praxigento\Core\Data|null $data
     * @return string
     */
    public function generate(\Praxigento\Core\Data $data = null);
}