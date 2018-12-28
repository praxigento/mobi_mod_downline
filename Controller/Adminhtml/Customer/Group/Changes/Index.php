<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Controller\Adminhtml\Customer\Group\Changes;

use Praxigento\Downline\Config as Cfg;

class Index
    extends \Praxigento\Core\App\Action\Back\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::ACL_MAGE_CUST__GROUP;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CUSTOMER_GROUP_CHANGES;
        $breadcrumbLabel = 'Group Changes';
        $breadcrumbTitle = 'Group Changes';
        $pageTitle = 'Group Changes';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }
}