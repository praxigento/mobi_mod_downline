<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Downline\Service\Tree;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Downline\Service\Tree\Verify\Request as ARequest;
use Praxigento\Downline\Service\Tree\Verify\Response as AResponse;
use Praxigento\Downline\Service\Tree\Verify\Response\Entry as DEntry;

class Verify
{
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Api\Helper\Tree */
    private $hlpTree;

    public function __construct(
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Api\Helper\Tree $hlpTree
    ) {
        $this->daoDwnlCust = $daoDwnlCust;
        $this->hlpTree = $hlpTree;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     * @throws
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);

        $tree = $this->daoDwnlCust->get();
        $mapById = $this->hlpTree->mapById($tree, EDwnlCust::A_CUSTOMER_REF);
        $mapByDepth = $this->hlpTree->mapByTreeDepthAsc($tree, EDwnlCust::A_CUSTOMER_REF, EDwnlCust::A_DEPTH);

        $entries = [];
        foreach ($mapByDepth as $level) {
            foreach ($level as $custId) {
                /** @var EDwnlCust $cust */
                $cust = $mapById[$custId];
                $pathAct = $cust->getPath();
                $parentId = $cust->getParentRef();
                /** @var EDwnlCust $parent */
                $parent = $mapById[$parentId];
                $pathParent = $parent->getPath();
                if ($custId != $parentId) {
                    $pathExp = $pathParent . $parentId . Cfg::DTPS;
                } else {
                    $pathExp = Cfg::DTPS;
                }
                if ($pathAct != $pathExp) {
                    $entry = new DEntry();
                    $entry->setCustomerId($custId);
                    $entry->setParentId($parentId);
                    $entry->setActPath($pathAct);
                    $entry->setExpPath($pathExp);
                    $entries[$custId] = $entry;
                }
            }
        }

        $result = new AResponse();
        $result->setEntries($entries);
        $result->markSucceed();
        return $result;
    }
}