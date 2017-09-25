<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class PhpVersion
 *
 * Dashboard row to check PHP version
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class PhpVersion extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle(__('PHP Version'));
        $this->setButtons('[devdocs-guides]/config-guide/prod/prod_perf-optimize.html' .
            '#server---software-recommendations');

        $phpVersionSplit = explode('-', PHP_VERSION, 2);
        $showVersion = reset($phpVersionSplit);
        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $this->setStatus(self::STATUS_OK);
            $this->setInfo(sprintf(__("PHP Version: %s\n"), $showVersion));
        } else {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setInfo(sprintf(__("PHP Version %s is older than 7.0")."\n", $showVersion));
            $this->setAction(__("Upgrade to PHP 7.0 or higher")."\n");
        }
    }
}
