<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service;

use Praxigento\Downline\Service\Map\Request;
use Praxigento\Downline\Service\Map\Response;

/**
 * @deprecated old-style service, should be split to separate operations.
 */
interface IMap {

    /**
     * @param Request\ById $req
     *
     * @return Response\ById
     */
    public function byId(Map\Request\ById $req);

    /**
     * @param Request\TreeByDepth $req
     *
     * @return Response\TreeByDepth
     */
    public function treeByDepth(Map\Request\TreeByDepth $req);

    /**
     * @param Request\TreeByTeams $req
     *
     * @return Response\TreeByTeams
     */
    public function treeByTeams(Map\Request\TreeByTeams $req);

}