<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Helper\Referral;

class CodeGenerator
    implements \Praxigento\Downline\Api\Helper\Referral\CodeGenerator
{
    public function generate(\Praxigento\Core\Data $data = null) {
        return 'referral code';
    }

}