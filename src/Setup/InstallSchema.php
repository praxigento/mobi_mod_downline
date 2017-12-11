<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Setup;

use Praxigento\Downline\Repo\Entity\Data\Change;
use Praxigento\Downline\Repo\Entity\Data\Customer;
use Praxigento\Downline\Repo\Entity\Data\Snap;

class InstallSchema extends \Praxigento\Core\App\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Downline';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Customer */
        $entityAlias = Customer::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Customer');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Change */
        $entityAlias = Change::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Change');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Snapshot */
        $entityAlias = Snap::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Snapshot');
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }


}