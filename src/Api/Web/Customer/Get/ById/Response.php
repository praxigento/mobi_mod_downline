<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Customer\Get\ById;

class Response
    extends \Praxigento\Core\Api\Web\Customer\Get\ById\Response
{
    /**
     * @return \Praxigento\Downline\Api\Service\Customer\Get\ById\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function getData() {
        return parent::getData();

    }

    /**
     * @param \Praxigento\Downline\Api\Service\Customer\Get\ById\Response $data
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function setData($data) {
        parent::setData($data);
    }

}