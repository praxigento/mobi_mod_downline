<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Query\Snap\OnDate;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class BuilderTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{

    public function test_getSelectQuery()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Praxigento\Downline\Repo\Query\Snap\OnDate\Builder $builder */
        $builder = $obm->get(\Praxigento\Downline\Repo\Query\Snap\OnDate\Builder::class);
        $query = $builder->getSelectQuery();
        $this->assertNotNull($query);
    }

}