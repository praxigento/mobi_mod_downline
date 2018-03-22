<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Snap;

use Praxigento\Downline\Api\Service\Snap\Clean\Request as ARequest;
use Praxigento\Downline\Api\Service\Snap\Clean\Response as AResponse;
use Praxigento\Downline\Repo\Data\Snap as ESnap;

/**
 * Public service to clean up all snapshots for downline tree.
 */
class Clean
    implements \Praxigento\Downline\Api\Service\Snap\Clean
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
    }

    /**
     * @param \Praxigento\Downline\Api\Service\Snap\Clean\Request $request
     * @return \Praxigento\Downline\Api\Service\Snap\Clean\Response
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** perform processing */
        $tbl = $this->resource->getTableName(ESnap::ENTITY_NAME);
        $query = "TRUNCATE TABLE `$tbl`";
        $this->conn->query($query);
        /** compose result */
        $result = new AResponse();
        return $result;
    }
}