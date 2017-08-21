<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class AppStateMode
 *
 * Dashboard row to check PHP version and settings
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class PhpSettings extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    private $exactValues = [
        'opcache.enable_cli' => 1,
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
        $this->setTitle(__('PHP Settings'));

        $phpVersionSplit = explode('-', PHP_VERSION, 2);
        $showVersion = reset($phpVersionSplit);
        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $this->info .= sprintf(__("PHP Version: %s\n"), $showVersion);
        } else {
            $this->problems .= sprintf(__("PHP Version %s is older than 7.0")."\n", $showVersion);
            $this->actions .= __("Upgrade to PHP 7.0 or higher")."\n";
        }
        foreach ($this->exactValues as $key => $value) {
            $curValue = ini_get($key);
            if (false === $curValue) {
                $this->problems .= $this->getProblem($key, $curValue);
                $this->actions .= sprintf(__("Set PHP ini setting '%s' to '%s'")."\n", $key, $value);
            } elseif (ini_get($key) != $value) {
                $this->problems .= $this->getProblem($key, $curValue);
                $this->actions .= sprintf(__("Change '%s' to '%s'")."\n", $key, $value);
            }
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
