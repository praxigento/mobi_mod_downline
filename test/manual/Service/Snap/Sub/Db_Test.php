<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap\Sub;

use Praxigento\Downline\Lib\Context;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Db_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase {

    public function test_getSnapMaxDate() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $sub \Praxigento\Downline\Lib\Service\Snap\Sub\Db */
        $sub = $obm->get('Praxigento\Downline\Lib\Service\Snap\Sub\Db');
        $snapMaxDate = $sub->getSnapMaxDatestamp();
        $this->assertNotNull($snapMaxDate);
    }

}