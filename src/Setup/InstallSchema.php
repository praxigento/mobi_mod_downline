<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Praxigento\Core\Lib\Setup\Db as Db;
use Praxigento\Downline\Lib\Entity\Change;
use Praxigento\Downline\Lib\Entity\Customer;
use Praxigento\Downline\Lib\Entity\Snap;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Read and parse JSON schema.
         */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Downline';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Customer */
        $entityAlias = Customer::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Customer'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Change */
        $entityAlias = Change::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Change'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Snapshot */
        $entityAlias = Snap::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Snapshot'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }


}