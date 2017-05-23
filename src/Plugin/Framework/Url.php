<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Framework;

class Url
{

    public function aroundGetRouteUrl(
        \Magento\Framework\Url $subject,
        \Closure $proceed,
        $routePath = null,
        $routeParams = null
    ) {
        $result = $proceed($routePath, $routeParams);
        $tail = strstr($routePath, '/dcp/#/');
        if ($tail != false) {
            $result = str_replace('/dcp/#/', $tail, $result);
        }
        return $result;
    }
}