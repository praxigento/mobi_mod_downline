<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Cli\Tree;

/**
 * Clean up all snapshots for downline tree.
 */
class Clean
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj
    )
    {
        parent::__construct(
            $manObj,
            'prxgt:downline:clean',
            'Clean up all snapshots for downline tree.'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        $output->writeln("<info>'{$this->getName()}' is started.<info>");
        $output->writeln('<info>Command is completed.<info>');

    }

}