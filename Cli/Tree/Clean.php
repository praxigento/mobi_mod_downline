<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Cli\Tree;

use Praxigento\Downline\Api\Service\Snap\Clean\Request as ARequest;
use Praxigento\Downline\Api\Service\Snap\Clean\Response as AResponse;

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
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $manObj,
            'prxgt:downline:clean',
            'Clean up all snapshots for downline tree.'
        );
        $this->servClean = $servClean;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $output->writeln('<info>Command \'' . $this->getName() . '\':<info>');
        $req = new ARequest();
        /** @var AResponse $resp */
        $resp = $this->servClean->exec($req);
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

}