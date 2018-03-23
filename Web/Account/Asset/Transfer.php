<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Web\Account\Asset;

use Praxigento\Downline\Api\Web\Account\Asset\Transfer\Request as ARequest;
use Praxigento\Downline\Api\Web\Account\Asset\Transfer\Response as AResponse;
use Praxigento\Downline\Config as Cfg;

class Transfer
    implements \Praxigento\Downline\Api\Web\Account\Asset\TransferInterface
{
    /** @var \Praxigento\Core\Api\App\Web\Authenticator */
    private $auth;
    /** @var \Praxigento\Downline\Helper\Downline */
    private $hlpDwnl;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Transfer */
    private $servAssetTransfer;

    public function __construct(
        \Praxigento\Core\Api\App\Web\Authenticator\Front $auth,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer,
        \Praxigento\Downline\Helper\Downline $hlpDwnl
    ) {
        $this->auth = $auth;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->servAssetTransfer = $servAssetTransfer;
        $this->hlpDwnl = $hlpDwnl;
    }


    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $amount = $data->getAmount();
        $assetTypeId = $data->getAssetId();
        $partyId = $data->getCounterPartyId();

        /* input data filters */
        $amount = abs($amount);
        $isDirect = false; // customer cannot initiate direct transfer
        $custId = $this->auth->getCurrentUserId($request); // customer can transfer FROM his account only
        list($isInDownline, $hasTheSameCountry) = $this->validate($custId, $partyId);
        if (!$isInDownline || !$hasTheSameCountry) {
            $phrase = new \Magento\Framework\Phrase('User is not authorized to perform this operation.');
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Magento\Framework\Exception\AuthorizationException($phrase);
        }

        /** perform processing */
        $req = new \Praxigento\Accounting\Service\Account\Asset\Transfer\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetTypeId);
        $req->setCounterPartyId($partyId);
        $req->setCustomerId($custId);
        $req->setIsDirect($isDirect);
        $resp = $this->servAssetTransfer->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }

    /**
     * Validate transfer conditions.
     *
     * @param int $custId
     * @param int $partyId
     * @return array [isInDownline, hasTheSameCountry]
     */
    private function validate($custId, $partyId)
    {
        $custData = $this->daoDwnlCust->getById($custId);
        $partyData = $this->daoDwnlCust->getById($partyId);
        /* validate downline */
        $custPath = $custData->getPath();
        $custPathFull = $custPath . $custId . Cfg::DTPS;
        $partyPath = $partyData->getPath();
        $isInDownline = (strpos($partyPath, $custPathFull) === 0);
        /* validate country */
        $custCountry = $custData->getCountryCode();
        $partyCountry = $partyData->getCountryCode();
        $hasTheSameCountry = ($custCountry == $partyCountry);
        return [$isInDownline, $hasTheSameCountry];
    }
}