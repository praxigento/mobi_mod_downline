<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Customer\Search;

/**
 * Request to get suggestions for customers by key (name/email/mlm_id).
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\Api\Request
{
    const LIMIT = 'limit';
    const SEARCH_KEY = 'searchKey';

    /**
     * @return int|null
     */
    public function getLimit()
    {
        $result = parent::get(self::LIMIT);
        return $result;
    }

    /**
     * @return string
     */
    public function getSearchKey()
    {
        $result = parent::get(self::SEARCH_KEY);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setLimit($data)
    {
        parent::set(self::LIMIT, $data);
    }

    /**
     * @param string $data
     */
    public function setSearchKey($data)
    {
        parent::set(self::SEARCH_KEY, $data);
    }
}