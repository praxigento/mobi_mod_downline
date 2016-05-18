<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Test\Story01;


use Praxigento\Core\Test\BaseIntegrationTest;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Data\Entity\Snap;
use Praxigento\Downline\Service\Customer\Request\ChangeParent as CustomerChangeParentRequest;
use Praxigento\Downline\Service\Snap\Request\Calc as SnapCalcRequest;
use Praxigento\Downline\Service\Snap\Request\GetStateOnDate as SnapGetStateOnDateRequest;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Main_IntegrationTest extends BaseIntegrationTest
{
    const DATE_UP_TO = '20151231';
    /** @var \Praxigento\Downline\Service\Snap\Call */
    private $_callSnap;
    /**
     * Date stamp for 'today' (float value from self::DEFAULT_DATE_BEGIN and up to self::DATE_UP_TO).
     * There are 13 customers in downline and each customer is created day by day started from 20151201.
     * @var
     */
    private $_dtToday = '20151213';

    public function __construct()
    {
        parent::__construct();
        $this->_callSnap = $this->_manObj->get(\Praxigento\Downline\Service\Snap\Call::class);
    }

    private function _calcSnapshots()
    {
        /* get snapshots before calcl */
        $reqSnap = new SnapGetStateOnDateRequest();
        $reqSnap->setDatestamp($this->_dtToday);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $snap = $respSnap->getData();
        $this->assertTrue(count($snap) == 0);
        /* calculate snapshots */
        $this->_logger->debug("Calculate snapshots.");
        $req = new SnapCalcRequest();
        $req->setDatestampTo($this->_dtToday);
        $respCalc = $this->_callSnap->calc($req);
        $this->assertTrue($respCalc->isSucceed());
        /* get snapshot after calculation */
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $snap = $respSnap->getData();
        $this->assertTrue(count($snap) == 13);
    }

    private function _changeC10ParentFromC7ToC9()
    {
        $this->_logger->debug("Change parent from #9 to #7 for customer #10.");
        $this->_dayIsOver();
        /** @var  $period \Praxigento\Core\Tool\IPeriod */
        $period = $this->_toolPeriod;
        $customerId = $this->_mapCustomerMageIdByIndex[10];
        $parentId = $this->_mapCustomerMageIdByIndex[9];
        /* get snapshot before calculation */
        $reqSnap = new SnapGetStateOnDateRequest();
        $reqSnap->setDatestamp($this->_dtToday);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $beforeData = $respSnap->getData($customerId);
        $beforeParentId = $beforeData[Snap::ATTR_PARENT_ID];
        $this->assertNotEquals($parentId, $beforeParentId);
        $this->_logger->debug("Mage ID of the #10 customer's parent is $beforeParentId (before update).");
        /* change parent */
        $reqChange = new CustomerChangeParentRequest();
        $reqChange->setCustomerId($customerId);
        $reqChange->setNewParentId($parentId);
        $reqChange->setDate($period->getTimestampFrom($this->_dtToday));
        $respChange = $this->_callDownlineCustomer->changeParent($reqChange);
        $this->assertTrue($respChange->isSucceed());
        /* calculate snapshots */
        $this->_logger->debug("Calculate snapshots.");
        $req = new SnapCalcRequest();
        $req->setDatestampTo($this->_dtToday);
        $respCalc = $this->_callSnap->calc($req);
        $this->assertTrue($respCalc->isSucceed());
        /* get snapshot after calculation */
        $reqSnap->setDatestamp($this->_dtToday);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $afterData = $respSnap->getData($customerId);
        $afterParentId = $afterData[Snap::ATTR_PARENT_ID];
        $this->assertNotEquals($beforeParentId, $afterParentId);
        $this->_logger->debug("Mage ID of the #10 customer's parent is $afterParentId (after update).");
    }

    private function _changeC10ParentFromC9ToC7()
    {
        $this->_logger->debug("Change parent back from #7 to #9 for customer #10.");
        $this->_dayIsOver();
        /** @var  $period \Praxigento\Core\Tool\IPeriod */
        $period = $this->_toolPeriod;
        $customerId = $this->_mapCustomerMageIdByIndex[10];
        $parentId = $this->_mapCustomerMageIdByIndex[7];
        /* get snapshot before calculation */
        $reqSnap = new SnapGetStateOnDateRequest();
        $reqSnap->setDatestamp($this->_dtToday);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $beforeData = $respSnap->getData($customerId);
        $beforeParentId = $beforeData[Snap::ATTR_PARENT_ID];
        $this->assertNotEquals($parentId, $beforeParentId);
        $this->_logger->debug("Mage ID of the #10 customer's parent is $beforeParentId (before update).");
        /* change parent */
        $reqChange = new CustomerChangeParentRequest();
        $reqChange->setCustomerId($customerId);
        $reqChange->setNewParentId($parentId);
        $reqChange->setDate($period->getTimestampFrom($this->_dtToday));
        $respChange = $this->_callDownlineCustomer->changeParent($reqChange);
        $this->assertTrue($respChange->isSucceed());
        /* calculate snapshots */
        $this->_logger->debug("Calculate snapshots.");
        $req = new SnapCalcRequest();
        $req->setDatestampTo($this->_dtToday);
        $respCalc = $this->_callSnap->calc($req);
        $this->assertTrue($respCalc->isSucceed());
        /* get snapshot after calculation */
        $reqSnap->datestamp = $this->_dtToday;
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $afterData = $respSnap->getData($customerId);
        $afterParentId = $afterData[Snap::ATTR_PARENT_ID];
        $this->assertNotEquals($beforeParentId, $afterParentId);
        $this->_logger->debug("Mage ID of the #10 customer's parent is $afterParentId (after update).");
    }

    private function _changeC13ToRoot()
    {
        $this->_logger->debug("Change parent to root for customer #13.");
        $this->_dayIsOver();
        /** @var  $period \Praxigento\Core\Tool\IPeriod */
        $period = $this->_toolPeriod;
        $customerId = $this->_mapCustomerMageIdByIndex[13];
        /* get snapshot before calculation */
        $reqSnap = new SnapGetStateOnDateRequest();
        $reqSnap->setDatestamp($this->_dtToday);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $beforeData = $respSnap->getData($customerId);
        $beforeParentId = $beforeData[Snap::ATTR_PARENT_ID];
        $this->assertNotEquals($customerId, $beforeParentId);
        $this->_logger->debug("Mage ID of the #10 customer's parent is $beforeParentId (before update).");
        /* change parent */
        $reqChange = new CustomerChangeParentRequest();
        $reqChange->setCustomerId($customerId);
        $reqChange->setNewParentId($customerId);
        $reqChange->setDate($period->getTimestampFrom($this->_dtToday));
        $respChange = $this->_callDownlineCustomer->changeParent($reqChange);
        $this->assertTrue($respChange->isSucceed());
        /* calculate snapshots */
        $this->_logger->debug("Calculate snapshots.");
        $req = new SnapCalcRequest();
        $req->setDatestampTo($this->_dtToday);
        $respCalc = $this->_callSnap->calc($req);
        $this->assertTrue($respCalc->isSucceed());
        /* get snapshot after calculation */
        $reqSnap->datestamp = $this->_dtToday;
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $this->assertTrue($respSnap->isSucceed());
        $afterData = $respSnap->getData($customerId);
        $afterParentId = $afterData[Snap::ATTR_PARENT_ID];
        $this->assertNotEquals($beforeParentId, $afterParentId);
        $this->_logger->debug("Mage ID of the #13 customer's parent is $afterParentId (after update).");
    }

    private function _checkSnapsForC13()
    {
        $period = $this->_toolPeriod;
        $today = $this->_dtToday;
        $customerId = $this->_mapCustomerMageIdByIndex[13];
        $customer7Id = $this->_mapCustomerMageIdByIndex[7];
        $customer9Id = $this->_mapCustomerMageIdByIndex[9];
        /* today should be root */
        $reqSnap = new SnapGetStateOnDateRequest();
        $reqSnap->setDatestamp($today);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $data = $respSnap->getData($customerId);
        $path = $data[Snap::ATTR_PATH];
        $this->assertEquals(Cfg::DTPS, $path);
        $this->_logger->debug("Customer C13 is root node today.");
        /* day before should be under C7 */
        $dayBefore = $period->getPeriodPrev($today);
        $reqSnap->setDatestamp($dayBefore);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $data = $respSnap->getData($customerId);
        $path = $data[Snap::ATTR_PATH];
        $this->assertTrue(strpos($path, Cfg::DTPS . $customer7Id . Cfg::DTPS) !== false);
        $this->_logger->debug("Customer C13 is under C7 day before.");
        /* 2 days before should be under C7 */
        $twoDaysBefore = $period->getPeriodPrev($dayBefore);
        $reqSnap->setDatestamp($twoDaysBefore);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $data = $respSnap->getData($customerId);
        $path = $data[Snap::ATTR_PATH];
        $this->assertTrue(strpos($path, Cfg::DTPS . $customer9Id . Cfg::DTPS) !== false);
        $this->_logger->debug("Customer C13 is under C9 2 days before.");
        /* 3 days before should be under C9 */
        $threeDaysBefore = $period->getPeriodPrev($twoDaysBefore);
        $reqSnap->setDatestamp($threeDaysBefore);
        $respSnap = $this->_callSnap->getStateOnDate($reqSnap);
        $data = $respSnap->getData($customerId);
        $path = $data[Snap::ATTR_PATH];
        $this->assertTrue(strpos($path, Cfg::DTPS . $customer7Id . Cfg::DTPS) !== false);
        $this->_logger->debug("Customer C13 is under C7 again 3 days before.");
    }

    private function _dayIsOver()
    {
        $this->_dtToday = $this->_toolPeriod->getPeriodNext($this->_dtToday);
        $this->_logger->debug("Today is '{$this->_dtToday}'.");
    }

    public function test_main()
    {
        $this->_logger->debug('Story01 in Downline Integration tests is started.');
        $this->_conn->beginTransaction();
        try {
            $this->_createMageCustomers(13);
            $this->_createDownlineCustomers(self::DATE_PERIOD_BEGIN, true);
            $this->_calcSnapshots();
            $this->_changeC10ParentFromC7ToC9();
            $this->_changeC10ParentFromC9ToC7();
            $this->_changeC13ToRoot();
            $this->_checkSnapsForC13();
        } finally {
            // $this->_conn->commit();
            $this->_conn->rollBack();
        }
        $this->_logger->debug('Story01 in Downline Integration tests is completed, all transactions are rolled back.');
    }
}