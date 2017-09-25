<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class ComposerAutoloader
 *
 * Dashboard row to show if the Composer Autoloader is optimized
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class ComposerAutoloader extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\Filesystem\DirectoryList */
    private $directoryList;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        array $data = []
    ) {
    
        $this->directoryList = $directoryList;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle(__("Composer autoloader"));
        $this->setButtons('[devdocs-guides]/config-guide/prod/prod_perf-optimize.html#server---composer-optimization');

        /** @noinspection PhpUndefinedClassInspection */
        /** @var null|\Composer\Autoload\ClassLoader $classLoader */
        $classLoader = null;
        foreach (spl_autoload_functions() as $function) {
            /** @noinspection PhpUndefinedClassInspection */
            if (is_array($function) &&
                $function[0] instanceof \Composer\Autoload\ClassLoader ) {
                $classLoader = $function[0];
                break;
            }
        }

        if (empty($classLoader)) {
            $this->setStatus(self::STATUS_UNKNOWN);
            $this->setInfo(__("Could not find Composer AutoLoader."));
            return;
        }

        if (array_key_exists('Magento\Config\Model\Config', $classLoader->getClassMap())) {
            $this->setStatus(self::STATUS_OK);
            $this->setInfo(__("Composer's autoloader is optimized"));
        } else {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setInfo(__("Composer's autoloader is not optimized."));
            $this->setAction(__("Execute: 'composer install -o'"));
        }
    }
}
