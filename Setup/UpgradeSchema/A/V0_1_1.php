<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Setup\UpgradeSchema\A;

use Praxigento\Downline\Repo\Data\Change\Group as EGroup;

/**
 * Add group change events registry.
 */
class V0_1_1
{
    /** @var \Praxigento\Core\App\Setup\Dem\Tool */
    private $toolDem;

    public function __construct(
        \Praxigento\Core\App\Setup\Dem\Tool $toolDem

    ) {
        $this->toolDem = $toolDem;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Praxigento\Core\Data $demPackage
     */
    public function exec($setup, $demPackage = null)
    {
        $demEntity = $demPackage->get('package/Change/entity/Group');
        $this->toolDem->createEntity(EGroup::ENTITY_NAME, $demEntity);
    }
}