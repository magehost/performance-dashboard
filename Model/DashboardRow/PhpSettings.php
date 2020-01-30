<?php
/**
 * Performance Dashboard Extension for Magento 2
 *
 * PHP version 5
 *
 * @category  MageHost
 * @package   MageHost\PerformanceDashboard
 * @author    Jeroen Vermeulen <jeroen@magehost.pro>
 * @copyright 2019 MageHost BV (https://magehost.pro)
 * @license   https://opensource.org/licenses/MIT  MIT License
 * @link      https://github.com/magehost/performance-dashboard
 */

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;

/**
 * Class PhpSettings
 *
 * Dashboard row to check PHP configuration
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class PhpSettings extends DashboardRow implements DashboardRowInterface
{
    private $exactValues = [
        'opcache.enable_cli' => 1,
        'opcache.save_comments' => 1,
        // opcache.validate_timestamps = 0  // If you have very fast disk access you don't need this.
        'opcache.consistency_checks' => 0
    ];

    private $minimalValues = [
        'opcache.memory_consumption' => 512,
        'opcache.max_accelerated_files' => 100000
    ];

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setTitle(__('PHP Configuration'));
        $this->buttons[] = '[devdocs-guides]/install-gde/prereq/php-settings.html' .
        '#php-required-opcache';

        foreach ($this->exactValues as $key => $value) {
            $curValue = ini_get($key);
            if (false === $curValue) {
                $this->problems .= $this->getProblem($key, $curValue);
                $this->actions .= sprintf(__("Set PHP ini setting '%s' to '%s'")."\n", $key, $value);
            } elseif (ini_get($key) != $value) {
                $this->problems .= $this->getProblem($key, $curValue);
                $this->actions .= sprintf(__("Change '%s' to '%s'")."\n", $key, $value);
            }
            $this->info .= sprintf(__("%s = %s")."\n", $key, $curValue);
        }
        foreach ($this->minimalValues as $key => $value) {
            $curValue = ini_get($key);
            if (false === $curValue) {
                $this->problems .= $this->getProblem($key, $curValue);
                $this->actions .= sprintf(__("Set PHP setting '%s' to '%s' or higher")."\n", $key, $value);
            } elseif (ini_get($key) < $value) {
                $this->problems .= $this->getProblem($key, $curValue);
                $this->actions .= sprintf(__("Change '%s' to '%s' or higher")."\n", $key, $value);
            }
            $this->info .= sprintf(__("%s = %s")."\n", $key, $curValue);
        }

        $this->groupProcess();
    }

    private function getProblem($key, $curValue)
    {
        if (false === $curValue) {
            return sprintf(__("'%s' is not set")."\n", $key);
        } else {
            return sprintf(__("'%s' is not optimal: '%s'")."\n", $key, $curValue);
        }
    }
}
