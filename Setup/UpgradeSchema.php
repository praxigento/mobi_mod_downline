<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Setup;

class UpgradeSchema
    implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /** @var \Praxigento\Core\App\Setup\Dem\Tool */
    private $toolDem;
    /** @var \Praxigento\Downline\Setup\UpgradeSchema\A\V0_1_1 */
    private $v0_1_1;

    public function __construct(
        \Praxigento\Core\App\Setup\Dem\Tool $toolDem,
        \Praxigento\Downline\Setup\UpgradeSchema\A\V0_1_1 $v0_1_1

    ) {
        $this->toolDem = $toolDem;
        $this->v0_1_1 = $v0_1_1;
    }

    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();
        $version = $context->getVersion();

        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Downline';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        if ($version == '0.1.0') {
            $this->v0_1_1->exec($setup, $demPackage);
        }

        $setup->endSetup();
    }
}