<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Observer;

use Magento\Framework\Event\Observer;
use Praxigento\Downline\Api\Helper\Group\Transition as HTrans;
use Praxigento\Downline\Config as Cfg;

/**
 * Validate posted data before save (group switching, etc.).
 */
class AdminhtmlCustomerPrepareSave
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    private const DATA_CUSTOMER = 'customer';
    private const DATA_REQUEST = 'request';

    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /** @var \Praxigento\Downline\Api\Helper\Group\Transition */
    private $hlpGroupTrans;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Downline\Api\Helper\Group\Transition $hlpGroupTrans
    ) {
        $this->daoGeneric = $daoGeneric;
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
        $gidSaved = $this->getGroupIdSaved($custId);

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

    /**
     * Customer data in 'adminhtml_customer_prepare_save' event is equal to posted data.
     * We need get saved group ID.
     *
     * @see \Magento\Customer\Controller\Adminhtml\Index\Save::execute
     *
     * @param int|null $custId
     * @return int|null
     */
    private function getGroupIdSaved($custId)
    {
        $result = null;
        if ($custId) {
            $entity = Cfg::ENTITY_MAGE_CUSTOMER;
            $pk = [Cfg::E_CUSTOMER_A_ENTITY_ID => $custId];
            $found = $this->daoGeneric->getEntityByPk($entity, $pk);
            if (is_array($found)) {
                $result = $found[Cfg::E_CUSTOMER_A_GROUP_ID];
            }
        }
        return $result;

    }
}