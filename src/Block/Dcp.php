<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Block;

/**
 * DCP landing page.
 */
class Dcp
    extends \Magento\Framework\View\Element\Template
{
    /** @var string[] list of cached filenames for JS/CSS resources in production mode. */
    private $cacheResources;
    /** @var \Praxigento\Core\Api\Helper\Config */
    private $hlpConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Praxigento\Core\Api\Helper\Config $hlpConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->hlpConfig = $hlpConfig;
    }

    /**
     * @return bool
     */
    public function isDevMode()
    {
        $result = $this->hlpConfig->getApiAuthenticationEnabledDevMode();
        return $result;
    }

    /**
     * Print out filename for 'inline' bundle (hashed in production mode).
     */
    public function outInline()
    {
        $resources = $this->readResources();
        $name = $resources['inline'] ?? '';
        print $name;
    }

    /**
     * Print out filename for 'main' bundle (hashed in production mode).
     */
    public function outMain()
    {
        $resources = $this->readResources();
        $name = $resources['main'] ?? '';
        print $name;
    }

    /**
     * Print out filename for 'scripts' bundle (hashed in production mode).
     */
    public function outScripts()
    {
        $resources = $this->readResources();
        $name = $resources['scripts'] ?? '';
        print $name;
    }

    /**
     * Read hashed filenames for DCP UI component.
     *
     * @return string[]
     */
    private function readResources()
    {
        if (is_null($this->cacheResources)) {
            $this->cacheResources = [];
            $dir = BP . '/dcp';
            $resources = scandir($dir);
            foreach ($resources as $one) {
                if (strpos($one, 'inline.') === 0) {
                    $this->cacheResources['inline'] = $one;
                } elseif (strpos($one, 'main.') === 0) {
                    $this->cacheResources['main'] = $one;
                } elseif (strpos($one, 'scripts.') === 0) {
                    $this->cacheResources['scripts'] = $one;
                }
            }
        }
        return $this->cacheResources;
    }
}