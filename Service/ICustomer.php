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
     * Add new customer to downline and new entry to change log.
     *
     * @param Request\Add $request
     *
     * @return Response\Add
     *
     * @deprecated use \Praxigento\Downline\Api\Service\Customer\Add
     */
    public function add(Request\Add $request);

    /**
     * @param Request\ChangeParent $request
     *
     * @return Response\ChangeParent
     *
     * @deprecated use \Praxigento\Downline\Api\Service\Customer\ChangeParent
     */
    public function changeParent(Request\ChangeParent $request);

}