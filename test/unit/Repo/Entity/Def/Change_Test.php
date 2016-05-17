<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity\Def;

use Praxigento\Downline\Data\Entity\Change as Entity;
use Praxigento\Downline\Repo\Entity\IChange;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Change_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Change */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /** setup mocks for constructor */
        $mRsrc = $this->_mockResourceConnection($this->mConn);
        /** create object to test */
        $this->obj = new Change(
            $mRsrc,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IChange::class, $this->obj);
    }

}