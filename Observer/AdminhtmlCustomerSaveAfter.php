<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Observer;

use Magento\Framework\Exception\LocalizedException as AMageException;
use Magento\Framework\Phrase as APhrase;
use Praxigento\Downline\Api\Service\Customer\Parent\Change\Request as AChangeRequest;
use Praxigento\Downline\Api\Service\Customer\Parent\Change\Response as AChangeResponse;
use Praxigento\Downline\Block\Adminhtml\Customer\Edit\Tabs\Mobi\Info as ABlock;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

/**
 * Save additional attributes for customer form in adminhtml.
 * @see \Magento\Customer\Controller\Adminhtml\Index\Save::execute
 */
class AdminhtmlCustomerSaveAfter
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    private const DATA_CUSTOMER = 'customer';
    private const DATA_REQUEST = 'request';

    /** @var \Praxigento\Santegra\Repo\Own\Dao\Registry\Accred */
    private $daoAccred;
    /** @var \Praxigento\Santegra\Repo\Own\Dao\Cust\Attr\CheckEmail */
    private $daoCheckEmail;
    /** @var \Praxigento\Santegra\Repo\Own\Dao\Attr\Customer */
    private $daoCoappl;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Magento\Framework\Message\ManagerInterface */
    private $mgrMessage;
    /** @var \Praxigento\Downline\Api\Service\Customer\Parent\Change */
    private $servChange;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $mgrMessage,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Santegra\Repo\Own\Dao\Cust\Attr\CheckEmail $daoCheckEmail,
        \Praxigento\Santegra\Repo\Own\Dao\Cust\Attr\Coapplicant $daoCoappl,
        \Praxigento\Santegra\Repo\Own\Dao\Registry\Accred $daoAccred,
        \Praxigento\Downline\Api\Service\Customer\Parent\Change $servChange
    ) {
        $this->mgrMessage = $mgrMessage;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoCheckEmail = $daoCheckEmail;
        $this->daoCoappl = $daoCoappl;
        $this->daoAccred = $daoAccred;
        $this->servChange = $servChange;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** define local working data */
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $observer->getData(self::DATA_CUSTOMER);
        $custId = $customer->getId();

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getData(self::DATA_REQUEST);

        /* fields group is not available if customer did not select the tab with fields */
        $originalRequestData = $request->getPostValue();
        if (isset($originalRequestData['customer'][ABlock::TMPL_FLDGRP])) {
            $group = $originalRequestData['customer'][ABlock::TMPL_FLDGRP];
            /* load customer */
            $cust = $this->daoDwnlCust->getById($custId);
            /* own MLM ID */
            $ownMlmId = trim($group[ABlock::TMPL_FIELD_OWN_MLM_ID]);
            $this->updateMlmId($cust, $ownMlmId);
            /* country code */
            $countryCode = $group[ABlock::TMPL_FIELD_COUNTRY_CODE];
            $this->updateCountryCode($cust, $countryCode);
            /* parent */
            $parentMlmId = trim($group[ABlock::TMPL_FIELD_PARENT_MLM_ID]);
            $this->updateParent($cust, $parentMlmId);
        }
    }

    /**
     * @param EDwnlCust $cust
     * @param string $newMlmId
     * @throws \Exception
     */
    private function updateMlmId($cust, $newMlmId)
    {
        $mlmId = $cust->getMlmId();
        if (
            ($mlmId != $newMlmId) &&
            !empty($newMlmId)
        ) {
            $custId = $cust->getCustomerRef();
            $cust->setMlmId($newMlmId);
            try {
                $this->daoDwnlCust->updateById($custId, $cust);
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                $phrase = new APhrase(__("Cannot set new MLM ID (%1). Error: %2"), [$newMlmId, $msg]);
                $e = new AMageException($phrase);
                throw $e;
            }
        }
    }

    /**
     * @param EDwnlCust $cust
     * @param string $newCountryCode
     * @throws \Exception
     */
    private function updateCountryCode($cust, $newCountryCode)
    {
        $countryCode = $cust->getCountryCode();
        if (
            ($countryCode != $newCountryCode) &&
            !empty($newCountryCode)
        ) {
            $custId = $cust->getCustomerRef();
            $cust->setCountryCode($newCountryCode);
            try {
                $this->daoDwnlCust->updateById($custId, $cust);
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                $phrase = new APhrase(
                    __("Cannot set new country code (%1) for customer #%2. Error: %3"),
                    [$newCountryCode, $custId, $msg]
                );
                $e = new AMageException($phrase);
                throw $e;
            }
        }
    }

    /**
     * @param EDwnlCust $cust
     * @param string $parentMlmId
     * @throws \Exception
     */
    private function updateParent($cust, $parentMlmId)
    {
        $custId = $cust->getCustomerRef();
        $parentIdCurrent = $cust->getParentRef();
        $parent = $this->daoDwnlCust->getByMlmId($parentMlmId);
        if ($parent) {
            $parentIdNew = $parent->getCustomerRef();
            if ($parentIdCurrent != $parentIdNew) {
                $req = new AChangeRequest();
                $req->setCustomerId($custId);
                $req->setNewParentId($parentIdNew);
                /** @var AChangeResponse $resp */
                $resp = $this->servChange->exec($req);
            }
        } else {
            $phrase = new APhrase(__("Given parent (MLM ID: %1) does not exist."), [$parentMlmId]);
            $e = new AMageException($phrase);
            throw $e;
        }
    }
}
