<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Cli\Tree;

use Praxigento\Downline\Service\Tree\Verify\Request as ARequest;
use Praxigento\Downline\Service\Tree\Verify\Response as AResponse;

/**
 * Verify downline tree health.
 */
class Verify
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var \Praxigento\Downline\Service\Tree\Verify */
    private $servVerify;

    public function __construct(
        \Praxigento\Downline\Service\Tree\Verify $servVerify
    ) {
        parent::__construct(
            'prxgt:downline:verify',
            'Verify downline tree health.'
        );
        $this->servVerify = $servVerify;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $req = new ARequest();
        /** @var AResponse $resp */
        $resp = $this->servVerify->exec($req);
        $failures = $resp->getEntries();
        $total = count($failures);
        $this->logInfo("Total $total failures are found in downline tree.");
        foreach ($failures as $failure) {
            $custId = $failure->getCustomerId();
            $parentId = $failure->getParentId();
            $exp = $failure->getExpPath();
            $act = $failure->getActPath();
            $this->logInfo("Customer #$custId, parent #$parentId:");
            $this->logInfo("\texp: $exp");
            $this->logInfo("\tact: $act");
        }
    }

}