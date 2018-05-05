<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service;

use Praxigento\Downline\Service\Customer\Request;
use Praxigento\Downline\Service\Customer\Response;

/**
 * @deprecated old-style service, should be split to separate operations.
 */
interface ICustomer
{
    /**
     * @param Request\ChangeParent $request
     *
     * @return Response\ChangeParent
     *
     * @deprecated use \Praxigento\Downline\Api\Service\Customer\ChangeParent
     */
    public function changeParent(Request\ChangeParent $request);

}