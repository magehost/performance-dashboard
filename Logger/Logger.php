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

namespace MageHost\PerformanceDashboard\Logger;

/**
 * Class Logger
 *
 * We need this class to be able to use our own log handler.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Logger
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class Logger extends \Monolog\Logger
{
    // Fix for   phpcs --standard=MEQP2   warning
    const I_AM_NOT_EMPTY = true;
}
