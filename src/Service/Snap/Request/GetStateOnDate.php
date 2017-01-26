<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap\Request;

/**
 * @method int getRootId()
 * @method void setRootId(int $data)
 */
class GetStateOnDate
    extends \Praxigento\Core\Service\Base\Request
{
    /**
     * @var string 'YYYYMMDD'
     */
    const DATE_STAMP = 'datestamp';

    public function getDatestamp() {
        $result = $this->get(self::DATE_STAMP);
        return $result;
    }

    public function setDatestamp($data) {
        $this->set(self::DATE_STAMP, $data);
    }
}