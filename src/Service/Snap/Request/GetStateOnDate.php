<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap\Request;

/**
 * Class GetStateOnDate
 * @package Praxigento\Downline\Service\Snap\Request
 */
class GetStateOnDate extends \Praxigento\Core\Service\Base\Request {
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