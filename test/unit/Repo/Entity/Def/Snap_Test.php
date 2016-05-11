<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Praxigento\Downline\Data\Entity\Snap as Entity;
use Praxigento\Downline\Repo\Entity\ISnap;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Snap_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  \Mockery\MockInterface */
    private $mRsrcConn;
    /** @var  Snap */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->mRsrcConn = $this->_mockResourceConnection($this->mDba);
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->obj = new Snap(
            $this->mRsrcConn,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(ISnap::class, $this->obj);
    }

}