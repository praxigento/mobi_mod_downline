<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Tree\Verify;

/**
 * @method \Praxigento\Downline\Service\Tree\Verify\Response\Entry[] getEntries()
 * @method void setEntries(\Praxigento\Downline\Service\Tree\Verify\Response\Entry[] $data)
 */
class Response
    extends \Praxigento\Core\App\Service\Response
{
    const ENTRIES = 'entries';
}