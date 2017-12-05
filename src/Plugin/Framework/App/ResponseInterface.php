<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17.5.12
 * Time: 18:51
 */

namespace Praxigento\Downline\Plugin\Framework\App;

class ResponseInterface
{
    /** @var \Praxigento\Downline\Tool\IReferral */
    protected $hlpRefCode;

    public function __construct(
        \Praxigento\Downline\Tool\IReferral $hlpRefCode
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