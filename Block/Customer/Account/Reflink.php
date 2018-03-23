<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Block\Customer\Account;

use Praxigento\Downline\Config as Cfg;

/**
 * see ./view/frontend/templates/downline/customer/account/reflink.phtml
 */
class Reflink
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Downline\Repo\Data\Customer */
    private $cacheDwnlCust;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $manStore;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->session = $session;
        $this->manStore = $manStore;
        $this->daoDwnlCust = $daoDwnlCust;
    }

    /**
     * Cached data for current downline customer.
     *
     * @return \Praxigento\Downline\Repo\Data\Customer
     */
    private function getDwnlCustomer()
    {
        if (is_null($this->cacheDwnlCust)) {
            $custId = $this->session->getCustomerId();
            $this->cacheDwnlCust = $this->daoDwnlCust->getById($custId);
        }
        return $this->cacheDwnlCust;
    }

    /** @return string */
    public function getMlmIdOwn()
    {
        $cust = $this->getDwnlCustomer();
        $result = $cust->getMlmId();
        return $result;
    }

    /** @return string */
    public function getMlmIdParent()
    {
        $cust = $this->getDwnlCustomer();
        $parentId = $cust->getParentId();
        $parent = $this->daoDwnlCust->getById($parentId);
        $result = $parent->getMlmId();
        return $result;
    }

    /** @return null|string */
    public function getReferralCode()
    {
        $dwnlCust = $this->getDwnlCustomer();
        $result = $dwnlCust->getReferralCode();
        return $result;
    }

    /** @return string */
    public function getReferralLink()
    {
        $refCode = $this->getReferralCode();
        $store = $this->manStore->getStore();
        $urlBase = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $result = $urlBase . '?' . Cfg::KEY_REF_CODE . '=' . $refCode;
        return $result;
    }
}