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

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Praxigento\Downline\Api\Service\Customer\Parent\Change */
    private $servParentChange;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Api\Service\Customer\Parent\Change $servParentChange
    ) {
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $manObj,
            'prxgt:downline:parent:change',
            'Change parent for the customer.'
        );
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
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

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->conn->beginTransaction();
        try {
            $output->writeln('<info>You can change parent using adminhtml. See "Customer Details / Santegra Info /  Parent MLM ID".<info>');
            $output->writeln('<info>Command \'' . $this->getName() . '\':<info>');

            /** define local working data */
            $custMlmId = $input->getOption(self::OPT_CUST_MLM_ID_NAME);
            $parentMlmId = $input->getOption(self::OPT_PARENT_MLM_ID_NAME);
            $newMlmId = $input->getOption(self::OPT_MLM_ID_NEW_NAME);
            $output->writeln("<info>Setup new parent '$parentMlmId' for customer '$custMlmId'.<info>");
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
                $output->writeln('<info>Operation is failed. See log for details.<info>');
            }

            /* set new MLM ID if requested */
            if ($newMlmId) {
                $this->setNewMlmId($custId, $newMlmId);
                $output->writeln("<info>New MLM ID '$newMlmId' is set for customer $custId (old: $custMlmId).<info>");
            }
            $this->conn->commit();
        } catch (\Throwable $e) {
            $output->writeln('<info>Command \'' . $this->getName() . '\' failed. Reason: '
                . $e->getMessage() . '.<info>');
            $this->conn->rollBack();
        }
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

    private function setNewMlmId($custId, $newMlmId)
    {
        $entity = new \Praxigento\Downline\Repo\Data\Customer();
        $entity->setMlmId($newMlmId);
        $this->daoDwnlCust->updateById($custId, $entity);
    }
}