<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Cli\Tree;

/**
 * Create snapshots for downline tree.
 */
class Snaps
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Downline\Api\Service\Snap\Calc */
    private $servSnapCalc;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Downline\Api\Service\Snap\Calc $servSnapCalc
    ) {
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $manObj,
            'prxgt:downline:snaps',
            'Create snapshots for downline tree.'
        );
        $this->manTrans = $manTrans;
        $this->servSnapCalc = $servSnapCalc;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        $output->writeln('<info>Command \'' . $this->getName() . '\':<info>');
        $def = $this->manTrans->begin();
        try {
            $req = new \Praxigento\Downline\Api\Service\Snap\Calc\Request();
            $this->servSnapCalc->exec($req);
            $this->manTrans->commit($def);
        } catch (\Throwable $e) {
            $output->writeln('<info>Command \'' . $this->getName() . '\' failed. Reason: '
                . $e->getMessage() . '.<info>');
        } finally {
            $this->manTrans->end($def);
        }
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

}