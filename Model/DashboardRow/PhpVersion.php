<?php
/**
 * Performance Dashboard Extension for Magento 2
 *
 * PHP version 5
 *
 * @category     MageHost
 * @package      MageHost\PerformanceDashboard
 * @author       Jeroen Vermeulen <jeroen@magehost.pro>
 * @copyright    2019 MageHost BV (https://magehost.pro)
 * @license      https://opensource.org/licenses/MIT  MIT License
 * @link         https://github.com/magehost/performance-dashboard
 * @noinspection PhpUndefinedMethodInspection
 */

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;

/**
 * Class PhpVersion
 *
 * Dashboard row to check PHP version
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class PhpVersion extends DashboardRow implements DashboardRowInterface
{
    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle(__('PHP Version'));
        $this->setButtons(
            '[devdocs-guides]/install-gde/system-requirements-tech.html#php'
        );

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
