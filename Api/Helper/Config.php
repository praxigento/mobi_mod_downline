<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Helper;

/**
 * Helper to get application level configuration parameters related to this module.
 */
interface Config
{

    /**
     * Group IDs of the customer groups that have a distributor permissions.
     *
     * @return int[]
     */
    public function getDowngradeGroupsDistrs();

    /**
     * Group where to unqualified customer will be placed after downgrade.
     *
     * @return int group ID
     */
    public function getDowngradeGroupUnqual();

    /**
     * Group ID for referral customers.
     * @return int
     *
     */
    public function getReferralsGroupReferrals();

    /**
     * ID for signup group of the referral customers.
     * @return int
     *
     */
    public function getReferralsGroupReferralsRegistered();

    /**
     * MLM ID for root customer to be parent for all anonymous.
     * @return string|false return 'false' if option is not set.
     */
    public function getReferralsRootAnonymous();
}