<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class PhpSettings
 *
 * Dashboard row to check PHP configuration
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
        $this->setTitle(__('PHP Configuration'));
        $this->buttons[] = '[devdocs-guides]/config-guide/prod/prod_perf-optimize.html#server---php-configuration';

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
