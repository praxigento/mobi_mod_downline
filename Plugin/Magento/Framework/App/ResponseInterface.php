<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17.5.12
 * Time: 18:51
 */

namespace Praxigento\Downline\Plugin\Magento\Framework\App;

class ResponseInterface
{
    /** @var \Praxigento\Downline\Api\Helper\Referral */
    protected $hlpRefCode;

    public function __construct(
        \Praxigento\Downline\Api\Helper\Referral $hlpRefCode
    )
    {
        $this->hlpRefCode = $hlpRefCode;
    }

    /**
     * @param \Magento\Framework\App\ResponseInterface $subject
     */
    public function beforeSendResponse(
        \Magento\Framework\App\ResponseInterface $subject
    )
    {
        $code = $this->hlpRefCode->getReferralCode();
        if ($code) {
            $this->hlpRefCode->setCookie($code);
        }
    }
}