<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Block\Customer\Account;

use Praxigento\Downline\Config as Cfg;

class Reflink
    extends \Magento\Framework\View\Element\Template
{
    /** @var string|null */
    private $cacheReferralCode;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $manStore;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->session = $session;
        $this->manStore = $manStore;
        $this->repoDwnlCust = $repoDwnlCust;
    }

    /**
     * Referral code itself.
     *
     * @return null|string
     */
    public function getReferralCode()
    {
        if (is_null($this->cacheReferralCode)) {
            $custId = $this->session->getCustomerId();
            if ($custId) {
                $dwnlCust = $this->repoDwnlCust->getById($custId);
                if ($dwnlCust) {
                    $this->cacheReferralCode = $dwnlCust->getReferralCode();
                } else {
                    $this->cacheReferralCode = '';
                }
            }
        }
        return $this->cacheReferralCode;
    }

    /**
     * @return string
     */
    public function getReferralLink()
    {
        $refCode = $this->getReferralCode();
        $store = $this->manStore->getStore();
        $urlBase = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $result = $urlBase . '?' . Cfg::KEY_REF_CODE . '=' . $refCode;
        return $result;
    }
}