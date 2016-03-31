<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service;

use Praxigento\Downline\Lib\Service\Map\Request;
use Praxigento\Downline\Lib\Service\Map\Response;

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