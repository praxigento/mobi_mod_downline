<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Setup;

use Praxigento\Downline\Repo\Data\Change;
use Praxigento\Downline\Repo\Data\Customer;
use Praxigento\Downline\Repo\Data\Snap;

class InstallSchema extends \Praxigento\Core\App\Setup\Schema\Base
{
    protected function setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Downline';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Customer */
        $demEntity = $demPackage->get('entity/Customer');
        $this->toolDem->createEntity(Customer::ENTITY_NAME, $demEntity);

        /* Change */
        $demEntity = $demPackage->get('entity/Change');
        $this->toolDem->createEntity(Change::ENTITY_NAME, $demEntity);

        /* Snapshot */
        $demEntity = $demPackage->get('entity/Snapshot');
        $this->toolDem->createEntity(Snap::ENTITY_NAME, $demEntity);
    }


}