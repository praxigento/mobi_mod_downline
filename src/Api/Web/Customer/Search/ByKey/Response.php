<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Customer\Search\ByKey;

class Response
    extends \Praxigento\Core\App\Api\Web\Response
{
    /**
     * @return \Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response\Data
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function getData() {
        return parent::getData();

    }

    /**
     * @param \Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response\Data $data
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function setData($data) {
        parent::setData($data);
    }

}