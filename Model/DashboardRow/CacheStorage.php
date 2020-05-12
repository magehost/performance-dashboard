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
use Magento\Framework\App\Cache\Frontend\Pool;

/**
 * Class CacheStorage
 *
 * Dashboard row to check if optimal cache storage is used.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class CacheStorage extends DashboardRow implements DashboardRowInterface
{
    /**
     * In-memory readonly pool of all cache front-end instances known to the system
     *
     * @var Pool
     */
    private $_cacheFrontendPool;

    /**
     * Constructor.
     *
     * Expects $data keys 'identifier' and 'name' to be set.
     *
     * @param Pool  $cacheFrontendPool -
     * @param array $data              -
     */
    public function __construct(
        Pool $cacheFrontendPool,
        array $data
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     *
     * @return void
     */
    public function load()
    {
        $this->setTitle(sprintf(__('%s Storage'), $this->getName()));
        $currentBackend = $this->_cacheFrontendPool->get(
            $this->getIdentifier()
        )->getBackend();
        $currentBackendClass = get_class($currentBackend);
        $this->setInfo(sprintf(__('%s'), $currentBackendClass));
        if (is_a($currentBackend, 'Cm_Cache_Backend_Redis')) {
            $this->setStatus(self::STATUS_OK);
        } elseif ('Zend_Cache_Backend_File' == $currentBackendClass) {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setAction(
                sprintf(__('%s is slow!'), $currentBackendClass) . "\n" .
                sprintf(
                    __('Store in Redis using Cm_Cache_Backend_Redis'),
                    $this->getName()
                )
            );
        } elseif (is_a($currentBackend, 'Cm_Cache_Backend_File')) {
            $this->setStatus(self::STATUS_WARNING);
            $this->setAction(
                sprintf(
                    __('Store in Redis using Cm_Cache_Backend_Redis'),
                    $this->getName()
                )
            );
        } else {
            $this->setStatus(self::STATUS_UNKNOWN);
            $this->setInfo(
                sprintf(
                    __("Unknown cache storage: '%s'"),
                    get_class($currentBackend)
                )
            );
        }
    }
}
