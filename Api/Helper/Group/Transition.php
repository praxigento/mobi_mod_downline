<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Helper\Group;

/**
 * Helper for customers downgrade functionality (transitions from distributors to regular customers).
 */
interface Transition
{
    const CTX_ADMIN = 'admin';

    /**
     * Validate does this group change is a downgrade.
     *
     * @param int $gidFrom
     * @param int $gidTo
     * @return bool
     */
    public function isDowngrade($gidFrom, $gidTo);

    /**
     * Validate does this group change is an upgrade.
     *
     * @param int $gidFrom
     * @param int $gidTo
     * @return bool
     */
    public function isUpgrade($gidFrom, $gidTo);

    /**
     * Validate conditions to change group ID for customer ($cust) from $gidFrom to $gidTo.
     * $ctx is an operation context (admin, cron, ... - I don't know yet what is it).
     *
     * @param int $gidFrom
     * @param int $gidTo
     * @param \Magento\Customer\Api\Data\CustomerInterface $cust
     * @param mixed $ctx
     * @return true
     */
    public function isAllowedGroupTransition($gidFrom, $gidTo, $cust = null, $ctx = null);
}