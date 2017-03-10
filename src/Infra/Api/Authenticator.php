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

    /** @var \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $repoDwnlCust;

    public function __construct(
        \Magento\Customer\Model\Session $sessCustomer,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoDwnlCust
    ) {
        parent::__construct($sessCustomer);
        $this->repoDwnlCust = $repoDwnlCust;
    }


    public function getCurrentUserData()
    {
        $result = parent::getCurrentUserData();
        $entityId = $result->get(Cfg::E_CUSTOMER_A_ENTITY_ID);
        $dwnlData = $this->repoDwnlCust->getById($entityId);
        $result->set(self::A_DWNL_DATA, $dwnlData);
        return $result;
    }

}