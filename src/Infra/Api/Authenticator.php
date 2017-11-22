<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Infra\Api;

use Praxigento\Downline\Config as Cfg;

class Authenticator
    extends \Praxigento\Core\Api\Authenticator
    implements \Praxigento\Core\Api\IAuthenticator
{
    const A_DWNL_DATA = 'dwnl_data';

    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    protected $repoDwnlCust;

    public function __construct(
        \Magento\Customer\Model\Session $sessCustomer,
        \Praxigento\Core\Helper\Config $hlpCfg,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust
    ) {
        parent::__construct($sessCustomer, $hlpCfg);
        $this->repoDwnlCust = $repoDwnlCust;
    }


    public function getCurrentCustomerData($offer = null)
    {
        /* load customer data into cache using parent loader */
        parent::getCurrentCustomerData($offer);
        if (
            !is_null($this->cacheCurrentCustomer) &&
            ($this->cacheCurrentCustomer->get(self::A_DWNL_DATA) == null)
        ) {
            /* add downline data to cache if missed */
            $entityId = $this->cacheCurrentCustomer->get(Cfg::E_CUSTOMER_A_ENTITY_ID);
            $dwnlData = new \Praxigento\Downline\Repo\Entity\Data\Customer();
            if ($entityId) {
                $dwnlData = $this->repoDwnlCust->getById($entityId);
            }
            $this->cacheCurrentCustomer->set(self::A_DWNL_DATA, $dwnlData);
        }
        return $this->cacheCurrentCustomer;
    }

}