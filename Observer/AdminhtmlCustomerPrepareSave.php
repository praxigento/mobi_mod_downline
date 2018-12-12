<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Observer;

use Magento\Framework\Event\Observer;
use Praxigento\Downline\Api\Helper\Group\Transition as HTrans;

/**
 * Validate posted data before save (group switching, etc.).
 */
class AdminhtmlCustomerPrepareSave
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    private const DATA_CUSTOMER = 'customer';
    private const DATA_REQUEST = 'request';

    /** @var \Praxigento\Core\Api\Helper\Customer\Group */
    private $hlpCustGroup;
    /** @var \Praxigento\Downline\Api\Helper\Group\Transition */
    private $hlpGroupTrans;

    public function __construct(
        \Praxigento\Core\Api\Helper\Customer\Group $hlpCustGroup,
        \Praxigento\Downline\Api\Helper\Group\Transition $hlpGroupTrans
    ) {
        $this->hlpCustGroup = $hlpCustGroup;
        $this->hlpGroupTrans = $hlpGroupTrans;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $observer->getData(self::DATA_CUSTOMER);
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getData(self::DATA_REQUEST);

        $custId = $customer->getId();
        $gidNew = $customer->getGroupId();
        $gidSaved = $this->hlpCustGroup->getIdByCustomerId($custId);

        $isAllowed = $this->hlpGroupTrans->isAllowedGroupTransition($gidSaved, $gidNew, $customer, HTrans::CTX_ADMIN);
        if (!$isAllowed) {
            $phrase = new \Magento\Framework\Phrase(
                'Group change (%1 => %2) does not allowed for customer #%3.',
                [$gidSaved, $gidNew, $custId]
            );
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Magento\Framework\Exception\AuthorizationException($phrase);
        }
    }
}