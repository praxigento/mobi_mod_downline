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
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Core\Api\Helper\Customer\Currency */
    private $hlpCustCurr;
    /** @var \Praxigento\Downline\Helper\Tree */
    private $hlpTree;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Transfer */
    private $servAssetTransfer;

    public function __construct(
        \Praxigento\Core\Api\App\Web\Authenticator\Front $auth,
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer,
        \Praxigento\Downline\Helper\Tree $hlpTree,
        \Praxigento\Core\Api\Helper\Customer\Currency $hlpCustCurr
    ) {
        $this->auth = $auth;
        $this->daoAcc = $daoAcc;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->servAssetTransfer = $servAssetTransfer;
        $this->hlpTree = $hlpTree;
        $this->hlpCustCurr = $hlpCustCurr;
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
        $isDirect = false; // customer cannot initiate direct transfer
        $custId = $this->auth->getCurrentUserId($request); // customer can transfer FROM his account only
        list($isInDownline, $hasTheSameCountry) = $this->validate($custId, $partyId);
        if (!$isInDownline || !$hasTheSameCountry) {
            $phrase = new \Magento\Framework\Phrase('User is not authorized to perform this operation.');
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Magento\Framework\Exception\AuthorizationException($phrase);
        }
        /* convert customer currency into wallet currency */
        $amount = abs($amount);
        $amount = $this->hlpCustCurr->convertToBase($amount, $custId);

        /* validate wallet balance */
        $isBalanceEnough = $this->validateBalance($custId, $assetTypeId, $amount);
        if (!$isBalanceEnough) {
            $phrase = new \Magento\Framework\Phrase('Customer has no enough balance to perform transfer.');
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Magento\Framework\Exception\AuthorizationException($phrase);
        }

        /** perform processing */
        $note = "Transferred by customer";

        $req = new \Praxigento\Accounting\Service\Account\Asset\Transfer\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetTypeId);
        $req->setCounterPartyId($partyId);
        $req->setCustomerId($custId);
        $req->setIsDirect($isDirect);
        $req->setNote($note);
        $resp = $this->servAssetTransfer->exec($req);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }

    /**
     * @param int $custId
     * @param int $assetTypeId
     * @param float $amount
     * @return bool 'true' if customer balance is greater then amount to transfer.
     */
    private function validateBalance($custId, $assetTypeId, $amount)
    {
        $result = false;
        $account = $this->daoAcc->getByCustomerId($custId, $assetTypeId);
        if ($account) {
            $balance = $account->getBalance();
            $result = ($balance > $amount);
        }
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