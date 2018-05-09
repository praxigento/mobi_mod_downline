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
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $manObj,
            'prxgt:downline:verify',
            'Verify downline tree health.'
        );
        $this->servVerify = $servVerify;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $output->writeln('<info>Command \'' . $this->getName() . '\':<info>');
        $req = new ARequest();
        /** @var AResponse $resp */
        $resp = $this->servVerify->exec($req);
        $failures = $resp->getEntries();
        $total = count($failures);
        $output->writeln("<info>Total $total failures are found in downline tree.<info>");
        foreach ($failures as $failure) {
            $custId = $failure->getCustomerId();
            $parentId = $failure->getParentId();
            $exp = $failure->getExpPath();
            $act = $failure->getActPath();
            $output->writeln("<info>Customer #$custId, parent #$parentId:<info>");
            $output->writeln("<info>\texp: $exp<info>");
            $output->writeln("<info>\tact: $act<info>");

        }
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

}