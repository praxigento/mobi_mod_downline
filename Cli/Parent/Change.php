<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Cli\Parent;

use Praxigento\Downline\Api\Service\Customer\Parent\Change\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Parent\Change\Response as AResponse;

/**
 * Change parent for the customer.
 */
class Change
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    const OPT_CUST_MLM_ID_NAME = 'customer';
    const OPT_CUST_MLM_ID_SHORTCUT = 'c';
    const OPT_MLM_ID_NEW_NAME = 'newId';
    const OPT_MLM_ID_NEW_SHORTCUT = 'i';
    const OPT_PARENT_MLM_ID_NAME = 'parent';
    const OPT_PARENT_MLM_ID_SHORTCUT = 'p';

    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Api\Service\Customer\Parent\Change */
    private $servParentChange;

    public function __construct(
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Api\Service\Customer\Parent\Change $servParentChange
    ) {
        parent::__construct(
            'prxgt:downline:parent:change',
            'Change parent for the customer.'
        );
        $this->daoDwnlCust = $daoDwnlCust;
        $this->servParentChange = $servParentChange;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_CUST_MLM_ID_NAME,
            self::OPT_CUST_MLM_ID_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            'MLM ID of the customer for whom parent being changed.'
        );
        $this->addOption(
            self::OPT_PARENT_MLM_ID_NAME,
            self::OPT_PARENT_MLM_ID_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            'MLM ID of the new parent.'
        );
        $this->addOption(
            self::OPT_MLM_ID_NEW_NAME,
            self::OPT_MLM_ID_NEW_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            'Set new MLM ID for the customer.'
        );
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $this->logInfo('You can change parent using adminhtml. See "Customer Details / Santegra Info /  Parent MLM ID".');
        /** define local working data */
        $custMlmId = $input->getOption(self::OPT_CUST_MLM_ID_NAME);
        $parentMlmId = $input->getOption(self::OPT_PARENT_MLM_ID_NAME);
        $newMlmId = $input->getOption(self::OPT_MLM_ID_NEW_NAME);
        $this->logInfo("Setup new parent '$parentMlmId' for customer '$custMlmId'.");
        $cust = $this->daoDwnlCust->getByMlmId($custMlmId);
        $parent = $this->daoDwnlCust->getByMlmId($parentMlmId);
        $custId = $cust->getCustomerRef();
        $parentId = $parent->getCustomerRef();

        $req = new ARequest();
        $req->setCustomerId($custId);
        $req->setNewParentId($parentId);
        /** @var AResponse $resp */
        $resp = $this->servParentChange->exec($req);
        if ($resp->getErrorCode() != $resp::ERR_NO_ERROR) {
            $this->logError('Operation is failed. See log for details.');
        }

        /* set new MLM ID if requested */
        if ($newMlmId) {
            $this->setNewMlmId($custId, $newMlmId);
            $this->logInfo("New MLM ID '$newMlmId' is set for customer $custId (old: $custMlmId).");
        }
    }

    private function setNewMlmId($custId, $newMlmId)
    {
        $entity = new \Praxigento\Downline\Repo\Data\Customer();
        $entity->setMlmId($newMlmId);
        $this->daoDwnlCust->updateById($custId, $entity);
    }
}