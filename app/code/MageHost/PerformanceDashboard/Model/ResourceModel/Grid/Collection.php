<?php

namespace MageHost\PerformanceDashboard\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Data\Collection
{
    /** @var \Magento\Framework\App\Cache\Frontend\Pool */
    protected $_cacheFrontendPool;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Framework\App\CacheInterface $cacheInterface,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($entityFactory);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $this->addItem( $this->getMageModeRow() );
            $this->addItem( $this->getCacheStorageRow('default','Magento Cache') );
            $this->addItem( $this->getCacheStorageRow('page_cache','Full Page Cache') );
            $this->addItem( $this->getCacheEnabledRow() );
            $this->addItem( $this->getSessionStorageRow() );
            $this->addItem( $this->getNonCachableTemplatesRow() );
            $this->addItem( $this->getConfigSettingRow('Use Flat Catalog Categories','catalog/frontend/flat_catalog_category',true) );
            $this->addItem( $this->getConfigSettingRow('Use Flat Catalog Products','catalog/frontend/flat_catalog_product',true) );
            $this->addItem( $this->getConfigSettingRow('Merge CSS Files','dev/css/merge_css_files',true) );
            $this->addItem( $this->getConfigSettingRow('Minify CSS Files','dev/css/minify_files',true) );
            $this->addItem( $this->getConfigSettingRow('Merge JavaScript Files','dev/js/merge_files',true) );
            $this->addItem( $this->getConfigSettingRow('Minify JavaScript Files','dev/js/minify_files',true) );
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    protected function getCacheStorageRow($identifier='default', $name='Magento Cache')
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( sprintf(__('%s Storage'),$name) );
        $currentBackend = $this->_cacheFrontendPool->get($identifier)->getBackend();
        if ( is_a($currentBackend,'Cm_Cache_Backend_Redis') ) {
            $result->setStatus(0);
            $result->setInfo( sprintf(__('Current storage: %s'), get_class($currentBackend)) );
        } elseif ( is_a($currentBackend,'Cm_Cache_Backend_File') ) {
            $result->setStatus(1);
            $result->setInfo( sprintf(__('Current storage: %s'), get_class($currentBackend)) );
            $result->setAction( sprintf(__('Switch to storing %s in Redis'),$name) );
        } else {
            $result->setStatus(3);
            $result->setInfo( sprintf(__('Unknown cache storage: %s'), get_class($currentBackend)) );
        }
        return $result;
    }

    protected function getMageModeRow()
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( 'Magento Mode' );
        $result->setInfo( 'TODO' );
        $result->setStatus(3);
        return $result;
    }

    protected function getCacheEnabledRow()
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( 'Cache Enabled' );
        $result->setInfo( 'TODO' );
        $result->setStatus(3);
        return $result;
    }

    protected function getSessionStorageRow()
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( 'Session Storage' );
        $result->setInfo( 'TODO' );
        $result->setStatus(3);
        return $result;
    }

    protected function getNonCachableTemplatesRow()
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( 'Non Cachable Templates' );
        $result->setInfo( 'TODO' );
        $result->setStatus(3);
        return $result;
    }

    protected function getConfigSettingRow($title, $path, $recommended)
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( $title );
        $result->setInfo( 'TODO '.$path );
        $result->setStatus(3);
        return $result;
    }
}
