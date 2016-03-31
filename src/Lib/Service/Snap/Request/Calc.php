<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Lib\Service\Snap\Request;


class Calc extends \Praxigento\Core\Lib\Service\Base\Request {
    /**
     * Calculate snapshots up to this date (including it).
     * @var string 'YYYYMMDD'
     */
    const DATE_STAMP_TO = 'datestamp_to';

    public function getDatestampTo() {
        $result = $this->getData(self::DATE_STAMP_TO);
        return $result;
    }

    public function setDatestampTo($data) {
        $this->setData(self::DATE_STAMP_TO, $data);
    }
}