<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Map;

use Praxigento\Core\Service\Base\Call as BaseCall;
use Praxigento\Downline\Lib\Service\IMap;
use Praxigento\Downline\Lib\Service\Map;

class Call extends BaseCall implements IMap
{

    public function byId(Map\Request\ById $req)
    {
        $result = new Response\ById();
        /* extract parameters from request */
        $asId = $req->getAsId();
        $toMap = $req->getDataToMap();
        $mapped = [];
        foreach ($toMap as $item) {
            $mapped[$item[$asId]] = $item;
        }
        $result->setMapped($mapped);
        $result->setAsSucceed();
        return $result;
    }

    public function treeByDepth(Map\Request\TreeByDepth $req)
    {
        $result = new Response\TreeByDepth();
        /* extract parameters from request */
        $keyCustId = $req->getAsCustomerId();
        $keyDepth = $req->getAsDepth();
        $toMap = $req->getDataToMap();
        $shouldReversed = $req->getShouldReversed();
        $mapped = [];
        foreach ($toMap as $item) {
            $customerId = $item[$keyCustId];
            $depth = $item[$keyDepth];
            if (!isset($mapped[$depth])) {
                $mapped[$depth] = [];
            }
            $mapped[$depth][] = $customerId;
        }
        if ($shouldReversed) {
            krsort($mapped);
        }
        $result->setMapped($mapped);
        $result->setAsSucceed();
        return $result;
    }

    public function treeByTeams(Map\Request\TreeByTeams $req)
    {
        $result = new Response\TreeByTeams();
        /* extract parameters from request */
        $keyCustId = $req->getAsCustomerId();
        $keyParentId = $req->getAsParentId();
        $toMap = $req->getDataToMap();
        $mapped = [];
        foreach ($toMap as $item) {
            $custId = $item[$keyCustId];
            $parentId = $item[$keyParentId];
            if ($custId == $parentId) {
                /* skip root nodes, root node is not a member of a team. */
                continue;
            }
            if (!isset($mapped[$parentId])) {
                $mapped[$parentId] = [];
            }
            $mapped[$parentId][] = $custId;
        }
        $result->setMapped($mapped);
        $result->setAsSucceed();
        return $result;
    }

}