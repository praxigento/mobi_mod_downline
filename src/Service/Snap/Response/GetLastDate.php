<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap\Response;


class GetLastDate extends \Praxigento\Core\App\Service\Base\Response
{
    const LAST_DATE = 'last_date';

    public function getLastDate()
    {
        $result = $this->get(self::LAST_DATE);
        return $result;
    }

    public function setLastDate($data)
    {
        $this->set(self::LAST_DATE, $data);
    }
}