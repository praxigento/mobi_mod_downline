<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Web\Account\Asset;

use Praxigento\Downline\Api\Web\Account\Asset\Transfer\Request as ARequest;
use Praxigento\Downline\Api\Web\Account\Asset\Transfer\Response as AResponse;

class Transfer
    implements \Praxigento\Downline\Api\Web\Account\Asset\TransferInterface
{
    /** @var \Praxigento\Core\App\Api\Web\IAuthenticator */
    private $auth;
    /** @var \Praxigento\Downline\Helper\Downline */
    private $hlpDwnl;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Transfer */
    private $servAssetTransfer;

    public function __construct(
        \Praxigento\Core\App\Api\Web\Authenticator\Front $auth,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer,
        \Praxigento\Downline\Helper\Downline $hlpDwnl
    ) {
        $this->auth = $auth;
        $this->repoDwnlCust = $repoDwnlCust;
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
        $isInDwnl = $this->isCounterPartyInDownline($custId, $partyId);
        if (!$isInDwnl) {
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
     * 'true' if $partyId is a child of the $custId.
     * @param $custId
     * @param $partyId
     * @return bool
     */
    private function isCounterPartyInDownline($custId, $partyId)
    {
        $result = false;
        $partyData = $this->repoDwnlCust->getById($partyId);
        if ($partyData) {
            $path = $partyData->getPath();
            $parents = $this->hlpDwnl->getParentsFromPath($path);
            $result = in_array($custId, $parents);
        }
        return $result;
    }
}