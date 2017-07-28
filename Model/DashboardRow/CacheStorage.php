<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

class CacheStorage extends \Magento\Framework\DataObject implements \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\Cache\Frontend\Pool */
    protected $_cacheFrontendPool;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param array $data -- expects keys 'identifier' and 'name' to be set.
     */
    public function __construct(
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        array $data
    )
    {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($data);

        $identifier = $this->getIdentifier();
        $name = $this->getName();

        $this->setTitle( sprintf(__('%s Storage'),$name) );
        $currentBackend = $this->_cacheFrontendPool->get($identifier)->getBackend();
        $currentBackendClass = get_class($currentBackend);
        $this->setInfo( sprintf(__('%s'),$currentBackendClass) );
        if ( is_a($currentBackend,'Cm_Cache_Backend_Redis') ) {
            $this->setStatus(0);
        } elseif ( 'Zend_Cache_Backend_File' == $currentBackendClass ) {
            $this->setStatus(2);
            $this->setAction( sprintf( __('%s is slow!'), $currentBackendClass ) . "\n" .
                sprintf( __('Store in Redis using Cm_Cache_Backend_Redis'), $name) );
        } elseif ( is_a($currentBackend,'Cm_Cache_Backend_File') ) {
            $this->setStatus(1);
            $this->setAction( sprintf(__('Store in Redis using Cm_Cache_Backend_Redis'),$name) );
        } else {
            $this->setStatus(3);
            $this->setInfo( sprintf(__("Unknown cache storage: '%s'"), get_class($currentBackend)) );
        }

    }
}