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

    /** @var \Praxigento\Downline\Api\Service\Snap\Calc */
    private $servSnapCalc;

    public function __construct(
        \Praxigento\Downline\Api\Service\Snap\Calc $servSnapCalc
    ) {
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            'prxgt:downline:snaps',
            'Create snapshots for downline tree.'
        );
        $this->servSnapCalc = $servSnapCalc;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $req = new \Praxigento\Downline\Api\Service\Snap\Calc\Request();
        $this->servSnapCalc->exec($req);
    }

}