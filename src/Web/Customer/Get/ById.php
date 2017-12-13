<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Web\Customer\Get;

use Praxigento\Downline\Api\Web\Customer\Get\ById\Request as ARequest;
use Praxigento\Downline\Api\Web\Customer\Get\ById\Response as AResponse;

class ById
    implements \Praxigento\Downline\Api\Web\Customer\Get\ByIdInterface
{
    /**
     * @param ARequest $request
     * @return AResponse|void
     */
    public function exec($request) {
        assert($request instanceof ARequest);
        /** define local working data */

        /** perform processing */

        /** compose result */
        $result = new AResponse();
        return $result;
    }
}