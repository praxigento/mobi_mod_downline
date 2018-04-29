<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Plugin\Framework\Mail\Template;


class TransportBuilder
{
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;

    public function __construct(
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust
    ) {
        $this->daoDwnlCust = $daoDwnlCust;
    }

    public function beforeSetTemplateVars(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateVars
    ) {
        /* see \Magento\Customer\Model\EmailNotification::newAccount*/
        if (is_array($templateVars) && isset($templateVars['customer'])) {
            $cust = $templateVars['customer'];
            if ($cust instanceof \Magento\Customer\Model\Data\CustomerSecure) {
                $custId = $cust->getId();
                $dwnl = $this->daoDwnlCust->getById($custId);
                $data = new \Magento\Framework\DataObject($dwnl->get());
                $templateVars['mobi_downline'] = $data;
            }
        }
        return [$templateVars];
    }
}