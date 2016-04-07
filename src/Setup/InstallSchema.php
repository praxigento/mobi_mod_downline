<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Setup;

use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Data\Entity\Snap;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Downline';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Customer */
        $entityAlias = Customer::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Customer');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Change */
        $entityAlias = Change::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Change');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Snapshot */
        $entityAlias = Snap::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Snapshot');
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }


}