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

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Praxigento\Downline\Api\Service\Snap\Calc */
    private $servSnapCalc;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Downline\Api\Service\Snap\Calc $servSnapCalc
    ) {
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $manObj,
            'prxgt:downline:snaps',
            'Create snapshots for downline tree.'
        );
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->servSnapCalc = $servSnapCalc;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        $output->writeln('<info>Command \'' . $this->getName() . '\':<info>');
        $this->conn->beginTransaction();
        try {
            $req = new \Praxigento\Downline\Api\Service\Snap\Calc\Request();
            $this->servSnapCalc->exec($req);
            $this->conn->commit();
        } catch (\Throwable $e) {
            $output->writeln('<info>Command \'' . $this->getName() . '\' failed. Reason: '
                . $e->getMessage() . '.<info>');
        } finally {
            $this->conn->rollBack();
        }
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

}