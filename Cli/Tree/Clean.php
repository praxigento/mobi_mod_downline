<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Cli\Tree;

use function Assert\that;
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

    /**
     * Override 'execute' method to prevent "DDL statements are not allowed in transactions" error.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|void|null
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* bind $output context to log-methods. */
        $this->setOut($output);
        $this->logInfo("Command '" . $this->getName() . "' is started.");
        try {
            $req = new ARequest();
            $this->servClean->exec($req);
        } catch (\Throwable $e) {
            $this->logError("Command '" . $this->getName() . "' failed. Reason: " . $e->getMessage());
        }
        $this->logInfo("Command '" . $this->getName() . "' is completed.");

    }


    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        /* this method is not used in this command */
    }

}