<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Controller\Customer;

use Praxigento\Accounting\Api\Data\Asset as DAsset;
use Praxigento\Accounting\Repo\Query\Asset\Get as QBGetAssets;

/**
 * Web API action to search customer by key (name, email, MLM ID).
 */
class Search
    extends \Praxigento\Core\App\Action\Front\Api\Base
{
    private $callCustSearch;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Core\Api\IAuthenticator $authenticator,
        \Praxigento\Downline\Api\Customer\SearchInterface $callCustSearch
    )
    {
        parent::__construct($context, $inputProcessor, $outputProcessor, $logger, $authenticator);
        $this->callCustSearch = $callCustSearch;
    }

    protected function getInDataType(): string
    {
        return \Praxigento\Downline\Api\Customer\Search\Request::class;
    }

    protected function getOutDataType(): string
    {
        return \Praxigento\Downline\Api\Customer\Search\Response::class;
    }

    /**
     * Load assets data from DB and compose API result component.
     *
     * @param int $custId
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset[]
     */
    private function loadAssetsData($custId)
    {
        $query = $this->qbGet->build();
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

            /* TODO: skip hidden types, like WALLET_HOLD */

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

    protected function process($request)
    {
        /* define local working data */
        assert($request instanceof \Praxigento\Downline\Api\Customer\Search\Request);
        $customerId = $request->getCustomerId();

        /* perform processing */
        $result = $this->callCustSearch->exec($request);

        /* compose result */
        return $result;
    }


}