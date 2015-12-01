<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Setup;

use Praxigento\Core\Lib\Setup\Schema\Base as SchemaBase;

class InstallSchema extends SchemaBase {

    /**
     * InstallSchema constructor.
     */
    public function __construct() {
        parent::__construct('\Praxigento\Downline\Lib\Setup\Schema');
    }
}