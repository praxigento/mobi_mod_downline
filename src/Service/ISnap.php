<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service;

use Praxigento\Downline\Service\Snap\Request;
use Praxigento\Downline\Service\Snap\Response;

/**
 * @deprecated old-style service, should be split to separate operations.
 */
interface ISnap {
    /**
     * Calculate downline snapshots up to requested date (including).
     *
     * @param Request\Calc $request
     *
     * @return Response\Calc
     */
    public function calc(Request\Calc $request);

    /**
     * Extend minimal Downline Tree Data (customer & parent) with depth and path.
     *
     * @param Request\ExpandMinimal $request
     *
     * @return Response\ExpandMinimal
     */
    public function expandMinimal(Request\ExpandMinimal $request);

    /**
     * Calculate the last date for existing downline snap or the "yesterday" for the first change log entry.
     *
     * @param Request\GetLastDate $request
     *
     * @return Response\GetLastDate
     */
    public function getLastDate(Request\GetLastDate $request);

    /**
     * Select downline tree state on the given datestamp.
     *
     * @param Request\GetStateOnDate $request
     *
     * @return Response\GetStateOnDate
     */
    public function getStateOnDate(Request\GetStateOnDate $request);

}