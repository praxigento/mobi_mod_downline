<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap\Response;


class GetLastDate extends \Praxigento\Core\Lib\Service\Base\Response {
    const LAST_DATE = 'last_date';

    public function getLastDate() {
        $result = $this->getData(self::LAST_DATE);
        return $result;
    }
}