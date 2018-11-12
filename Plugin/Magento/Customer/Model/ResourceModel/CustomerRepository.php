<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Magento\Customer\Model\ResourceModel;

use Praxigento\Downline\Repo\Data\Change as EChange;
use Praxigento\Downline\Repo\Data\Snap as ESnap;

/**
 * Remove customer related data from Downline on customer delete from adminhtml.
 */
class CustomerRepository
{
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    private $daoChange;
    /** @var \Praxigento\Downline\Repo\Dao\Snap */
    private $daoSnap;

    public function __construct(
        \Praxigento\Downline\Repo\Dao\Change $daoChange,
        \Praxigento\Downline\Repo\Dao\Snap $daoSnap
    ) {
        $this->daoChange = $daoChange;
        $this->daoSnap = $daoSnap;
    }

    /**
     * Remove customer related data from Downline on customer delete from adminhtml.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param $customerId
     * @return array
     */
    public function beforeDeleteById(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        $customerId
    ) {
        $this->deleteSnaps($customerId);
        $this->deleteChange($customerId);
        $result = [$customerId];
        return $result;
    }

    private function deleteChange($custId)
    {
        $where = EChange::A_CUSTOMER_ID . '=' . (int)$custId;
        $this->daoChange->delete($where);
    }

    private function deleteSnaps($custId)
    {
        $where = ESnap::A_CUSTOMER_ID . '=' . (int)$custId;
        $this->daoSnap->delete($where);
    }
}