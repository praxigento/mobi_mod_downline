<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Repo\Partial;


interface ICustomerGrid
{
    const AS_FLD_CUSTOMER_DEPTH = 'prxgtDwnlCustomerDepth';
    const AS_FLD_CUSTOMER_REF = 'prxgtDwnlCustomerRef';
    const AS_FLD_PARENT_ID = 'prxgtDwnlParentId';
    const AS_FLD_PARENT_REF = 'prxgtDwnlParentRef';
    const AS_TBL_CUST = 'prxgtDwnlCust';
    const AS_TBL_PARENT_CUST = 'prxgtDwnlParentCust';

    /**
     * @param \Magento\Framework\DB\Select $query
     * @return \Magento\Framework\DB\Select
     */
    public function populateSelect($query);

    /**
     * @param \Magento\Framework\DB\Select $query
     * @return \Magento\Framework\DB\Select
     */
    public function populateSelectCount($query);
}