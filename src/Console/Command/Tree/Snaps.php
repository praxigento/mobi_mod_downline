<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Console\Command\Tree;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create snapshots for downline tree.
 */
class Snaps
    extends \Praxigento\Core\Console\Command\Base
{
    /** @var \Praxigento\Downline\Service\ISnap */
    protected $callSnap;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Downline\Service\ISnap $callSnap
    ) {
        parent::__construct(
            $manObj,
            'prxgt:downline:snaps',
            'Create snapshots for downline tree.'
        );
        $this->callSnap = $callSnap;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $req = new \Praxigento\Downline\Service\Snap\Request\Calc();
        $req->setDatestampTo('21001231');
        $resp = $this->callSnap->calc($req);
        $succeed = $resp->isSucceed();
        if ($succeed) {
            $output->writeln('<info>Command is completed.<info>');
        } else {
            $output->writeln('<info>Command is failed.<info>');
        }

    }

}