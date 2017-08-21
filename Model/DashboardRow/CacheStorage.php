<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class CacheStorage
 *
 * Dashboard row to check if optimal cache storage is used.
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class CacheStorage extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\Cache\Frontend\Pool */
    private $cacheFrontendPool;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param array $data -- expects keys 'identifier' and 'name' to be set.
     */
    public function __construct(
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        array $data
    ) {
        $this->cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle(sprintf(__('%s Storage'), $this->getName()));
        $currentBackend = $this->cacheFrontendPool->get($this->getIdentifier())->getBackend();
        $currentBackendClass = get_class($currentBackend);
        $this->setInfo(sprintf(__('%s'), $currentBackendClass));
        if (is_a($currentBackend, 'Cm_Cache_Backend_Redis')) {
            $this->setStatus(self::STATUS_OK);
        } elseif ('Zend_Cache_Backend_File' == $currentBackendClass) {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setAction(sprintf(__('%s is slow!'), $currentBackendClass) . "\n" .
                sprintf(__('Store in Redis using Cm_Cache_Backend_Redis'), $this->getName()));
        } elseif (is_a($currentBackend, 'Cm_Cache_Backend_File')) {
            $this->setStatus(self::STATUS_WARNING);
            $this->setAction(sprintf(__('Store in Redis using Cm_Cache_Backend_Redis'), $this->getName()));
        } else {
            $this->setStatus(self::STATUS_UNKNOWN);
            $this->setInfo(sprintf(__("Unknown cache storage: '%s'"), get_class($currentBackend)));
        }
    }
}
