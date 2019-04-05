<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Cli\Tree;

use Praxigento\Downline\Api\Service\Snap\Clean\Request as ARequest;

/**
 * Clean up all snapshots for downline tree.
 */
class Clean
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var \Praxigento\Downline\Api\Service\Snap\Clean */
    private $servClean;

    public function __construct(
        \Praxigento\Downline\Api\Service\Snap\Clean $servClean
    ) {
        parent::__construct(
            'prxgt:downline:clean',
            'Clean up all snapshots for downline tree.'
        );
        $this->servClean = $servClean;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $req = new ARequest();
        $this->servClean->exec($req);
    }

}