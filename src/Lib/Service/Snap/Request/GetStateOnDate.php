<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service\Snap\Request;

/**
 * Class GetStateOnDate
 * @package Praxigento\Downline\Lib\Service\Snap\Request
 */
class GetStateOnDate extends \Praxigento\Core\Lib\Service\Base\Request {
    /**
     * @var string 'YYYYMMDD'
     */
    const DATE_STAMP = 'datestamp';

    public function getDatestamp() {
        $result = $this->getData(self::DATE_STAMP);
        return $result;
    }

    public function setDatestamp($data) {
        $this->setData(self::DATE_STAMP, $data);
    }
}