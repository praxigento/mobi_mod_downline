<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Customer\Search\ByKey;

class Response
    extends \Praxigento\Core\Api\App\Web\Response
{
    /**
     * @return \Praxigento\Downline\Api\Service\Customer\Search\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function getData()
    {
        return parent::getData();

    }

    /**
     * @param \Praxigento\Downline\Api\Service\Customer\Search\Response $data
     * @return void
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function setData($data)
    {
        parent::setData($data);
    }

}