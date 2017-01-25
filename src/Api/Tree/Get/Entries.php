<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Api\Tree\Get;

class Entries
    implements \Praxigento\Downline\Api\Tree\Get\EntriesInterface
{
    /** @var \Praxigento\Downline\Service\ISnap */
    protected $callSnap;

    public function __construct(
        \Praxigento\Downline\Service\ISnap $callSnap
    ) {
        $this->callSnap = $callSnap;
    }


    public function execute(\Praxigento\Downline\Api\Tree\Get\Entries\Request $data)
    {
        $maxDepth = $data->getMaxDepth();
        $maxEntries = $data->getMaxEntries();
        $period = $data->getPeriod();
        $rootNode = $data->getRootNode();
        $req = new \Praxigento\Downline\Service\Snap\Request\GetStateOnDate();
        $req->setDatestamp('20170131');
        $resp = $this->callSnap->getStateOnDate($req);
        return true;
    }
}