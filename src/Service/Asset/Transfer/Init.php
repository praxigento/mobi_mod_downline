<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Asset\Transfer;

use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Request as ARequest;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response as AResponse;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data as DRespData;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset as DAsset;
use Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Customer as DCustomer;
use Praxigento\Accounting\Service\Asset\Transfer\Init\Db\Query\GetAssets as QBGetAssets;
use Praxigento\Downline\Repo\Query\Customer\Get as QBGetCustomer;

/**
 * Override original service from 'Accounting' module to get initialization data for
 * asset transfer modal slider in adminhtml.
 */
class Init
    implements \Praxigento\Accounting\Api\Service\Asset\Transfer\Init
{
    /** @var \Praxigento\Accounting\Service\Asset\Transfer\Init\Db\Query\GetAssets */
    private $qbGetAssets;
    /** @var \Praxigento\Downline\Repo\Query\Customer\Get */
    private $qbGetCustomer;

    public function __construct(
        QBGetAssets $qbGetAssets,
        QBGetCustomer $qbGetCustomer
    )
    {
        $this->qbGetAssets = $qbGetAssets;
        $this->qbGetCustomer = $qbGetCustomer;
    }

    public function exec(ARequest $data)
    {
        /* define local working data */
        $customerId = $data->getCustomerId();

        /* perform processing */
        $assets = $this->loadAssetsData($customerId);
        $customer = $this->loadCustomerData($customerId);

        /* compose result */
        $respData = new DRespData();
        $respData->setAssets($assets);
        $respData->setCustomer($customer);
        $result = new AResponse();
        $result->setData($respData);
        return $result;
    }


    /**
     * Load assets data from DB and compose API result component.
     *
     * @param int $custId
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset[]
     */
    private function loadAssetsData($custId)
    {
        $query = $this->qbGetAssets->build();
        $conn = $query->getConnection();
        $bind = [
            QBGetAssets::BND_CUST_ID => $custId
        ];
        $rs = $conn->fetchAll($query, $bind);

        $result = [];
        foreach ($rs as $db) {
            /* extract DB data */
            $assetId = $db[QBGetAssets::A_ASSET_ID];
            $assetCode = $db[QBGetAssets::A_ASSET_CODE];
            $accId = $db[QBGetAssets::A_ACC_ID];
            $accBalance = $db[QBGetAssets::A_ACC_BALANCE];

            /* compose API data */
            $api = new DAsset();
            $api->setAccBalance($accBalance);
            $api->setAccId($accId);
            $api->setAssetCode($assetCode);
            $api->setAssetId($assetId);
            $result[] = $api;
        }
        return $result;
    }

    /**
     * Load customer data from DB and compose API result component.
     *
     * @param int $custId
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Customer
     */
    private function loadCustomerData($custId)
    {
        $query = $this->qbGetCustomer->build();
        $conn = $query->getConnection();
        $bind = [
            QBGetCustomer::BND_CUST_ID => $custId
        ];
        $rs = $conn->fetchRow($query, $bind);

        /* extract DB data */
        $custId = $rs[QBGetCustomer::A_ID];
        $email = $rs[QBGetCustomer::A_EMAIL];
        $nameFirst = $rs[QBGetCustomer::A_NAME_FIRST];
        $nameLast = $rs[QBGetCustomer::A_NAME_LAST];
        $mlmId = $rs[QBGetCustomer::A_MLM_ID];

        /* compose API data */
        $result = new DCustomer();
        $result->setId($custId);
        $result->setEmail($email);
        $result->setNameFirst($nameFirst);
        $result->setNameLast($nameLast);
        $result->setMlmId($mlmId);

        return $result;
    }
}