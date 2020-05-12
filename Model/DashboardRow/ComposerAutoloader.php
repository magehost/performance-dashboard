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

use Composer\Autoload\ClassLoader;
use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;

/**
 * Class ComposerAutoloader
 *
 * Dashboard row to show if the Composer Autoloader is optimized
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class ComposerAutoloader extends DashboardRow implements DashboardRowInterface
{
    /**
     * Load Row, is called by DashboardRowFactory
     *
     * @return void
     */
    public function load()
    {
        $this->setTitle(__("Composer autoloader"));
        $this->setButtons(
            '[devdocs-guides]/performance-best-practices/deployment-flow.html' .
            '#preprocess-dependency-injection-instructions'
        );

        /**
         * Find the \Composer\Autoload\ClassLoader class among the autoloaders
         *
         * @noinspection PhpUndefinedClassInspection
         * @var          null|ClassLoader $classLoader
         */
        $classLoader = null;
        foreach (spl_autoload_functions() as $function) {

            if (is_array($function)
                && $function[0] instanceof ClassLoader
            ) {
                $classLoader = $function[0];
                break;
            }
        }

        if (empty($classLoader)) {
            $this->setStatus(self::STATUS_UNKNOWN);
            $this->setInfo(__("Could not find Composer AutoLoader."));
            return;
        }

        if (array_key_exists(
            \Magento\Config\Model\Config::class,
            $classLoader->getClassMap()
        )) {
            $this->setStatus(self::STATUS_OK);
            $this->setInfo(__("Composer's autoloader is optimized"));
        } else {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setInfo(__("Composer's autoloader is not optimized."));
            $this->setAction(__("Execute: 'composer dump-autoload -o --apcu'"));
        }
    }
}
