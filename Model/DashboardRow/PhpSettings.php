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
        $this->setStatus(self::STATUS_OK);
        $this->setTitle(__('PHP Settings'));
        $info = '';
        $problems = '';
        $warnings = '';
        $actions = '';
        $phpVersionSplit = explode('-', PHP_VERSION, 2);
        $showVersion = reset($phpVersionSplit);
        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $info .= sprintf(__("PHP Version: %s\n"), $showVersion);
        } else {
            $problems .= sprintf(__("PHP Version %s is older than 7.0")."\n", $showVersion);
            $actions .= __("Upgrade to PHP 7.0 or higher")."\n";
        }
        foreach ($this->exactValues as $key => $value) {
            $curValue = ini_get($key);
            if (false === $curValue) {
                $problems .= $this->getProblem($key, $curValue);
                $actions .= sprintf(__("Set PHP ini setting '%s' to '%s'")."\n", $key, $value);
            } elseif (ini_get($key) != $value) {
                $problems .= $this->getProblem($key, $curValue);
                $actions .= sprintf(__("Change '%s' to '%s'")."\n", $key, $value);
            }
        }
        foreach ($this->minimalValues as $key => $value) {
            $curValue = ini_get($key);
            if (false === $curValue) {
                $problems .= $this->getProblem($key, $curValue);
                $actions .= sprintf(__("Set PHP setting '%s' to '%s' or higher")."\n", $key, $value);
            } elseif (ini_get($key) < $value) {
                $problems .= $this->getProblem($key, $curValue);
                $actions .= sprintf(__("Change '%s' to '%s' or higher")."\n", $key, $value);
            }
        }

        if ($problems) {
            $this->setStatus(self::STATUS_PROBLEM);
        } elseif ($warnings) {
            $this->setStatus(self::STATUS_WARNING);
        } else {
            $this->setStatus(self::STATUS_OK);
        };
        $this->setInfo($problems.$warnings.$info);
        $this->setAction($actions);
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
