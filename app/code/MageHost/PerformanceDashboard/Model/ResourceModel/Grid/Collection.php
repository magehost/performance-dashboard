<?php

namespace MageHost\PerformanceDashboard\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Data\Collection
{
    /** @var  \Magento\Framework\App\Cache\Frontend\Pool */
    protected $_cacheFrontendPool;

    /** @var  \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $_scopeConfig;

    /** @var  \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var  \Magento\Framework\App\State */
    protected $_appState;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
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
            $this->addItem( $this->getAppStateModeRow() );
            $this->addItem( $this->getCacheStorageRow('default','Magento Cache') );
            $this->addItem( $this->getCacheStorageRow('page_cache','Full Page Cache') );
            $this->addItem( $this->getCacheEnabledRow() );
            $this->addItem( $this->getSessionStorageRow() );
            $this->addItem( $this->getNonCachableTemplatesRow() );
            $this->addItem( $this->getConfigSettingRow('Use Flat Catalog Categories','catalog/frontend/flat_catalog_category',true) );
            $this->addItem( $this->getConfigSettingRow('Use Flat Catalog Products','catalog/frontend/flat_catalog_product',true) );
            $this->addItem( $this->getConfigSettingRow('Merge JavaScript Files','dev/js/merge_files',true) );
            $this->addItem( $this->getConfigSettingRow('Minify JavaScript Files','dev/js/minify_files',true) );
            $this->addItem( $this->getConfigSettingRow('Merge CSS Files','dev/css/merge_css_files',true) );
            $this->addItem( $this->getConfigSettingRow('Minify CSS Files','dev/css/minify_files',true) );
            // Idea: FPC hit / miss percentage
            // Idea: Cache flushes per hour
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    protected function getCacheStorageRow($identifier='default', $name='Magento Cache')
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( sprintf(__('%s Storage'),$name) );
        $currentBackend = $this->_cacheFrontendPool->get($identifier)->getBackend();
        $result->setInfo( sprintf(__('%s'), get_class($currentBackend)) );
        if ( is_a($currentBackend,'Cm_Cache_Backend_Redis') ) {
            $result->setStatus(0);
        } elseif ( is_a($currentBackend,'Cm_Cache_Backend_File') ) {
            $result->setStatus(1);
            $result->setAction( sprintf(__('Switch to storing %s in Redis'),$name) );
        } else {
            $result->setStatus(3);
            $result->setInfo( sprintf(__("Unknown cache storage: '%s'"), get_class($currentBackend)) );
        }
        return $result;
    }

    protected function getAppStateModeRow()
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( 'Magento Mode' );
        $result->setInfo( sprintf( __("Magento is running in '%s' mode"),
            $this->_appState->getMode() ) );
        if ( \Magento\Framework\App\State::MODE_PRODUCTION == $this->_appState->getMode() ) {
            $result->setStatus(0);
        } else {
            $result->setStatus(2);
            $result->setAction( __("Switch mode to Production") );
        }
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
        $info = array();
        $action = array();

        $defaultResult = $this->checkConfigSetting( $path, $recommended );
        $status = $defaultResult->getStatus();
        if ( 0 < $defaultResult->getStatus() ) {
            $info[] = $defaultResult->getInfo();
            $action[] = $defaultResult->getAction();
        }

        /** @var \Magento\Store\Api\Data\WebsiteInterface $website */
        foreach( $this->_storeManager->getWebsites() as $website ) {

            $websiteResult = $this->checkConfigSetting( $path, $recommended, $website );
            if ( $websiteResult->getStatus() > $defaultResult->getStatus() ) {
                $status = $websiteResult->getStatus();
                $info[] = $websiteResult->getInfo();
                $action[] = $websiteResult->getAction();
            }

            foreach( $this->_storeManager->getStores() as $store ) {
                if ( $store->getWebsiteId() == $website->getId() ) {
                    $storeResult = $this->checkConfigSetting( $path, $recommended, $store );
                    if ( $storeResult->getStatus() > $websiteResult->getStatus() ) {
                        $status = $storeResult->getStatus();
                        $info[] = $storeResult->getInfo();
                        $action[] = $storeResult->getAction();
                    }
                }
            }
        }

        if (0 == $status) {
            $result->setInfo( $defaultResult->getInfo() );
        } else {
            $result->setInfo(implode("\n",$info));
            $result->setAction(implode("\n",$action));
        }
        $result->setStatus($status);
        return $result;
    }

    protected function checkConfigSetting( $path,
                                           $recommended,
                                           $scope=null )
    {
        $result = new \Magento\Framework\DataObject;

        if ( is_null($scope) ) {
            $scopeType = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeCode = null;
            $showScope = __('in Default Config');
        }
        elseif ( $scope instanceof \Magento\Store\Api\Data\WebsiteInterface ) {
            $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
            $scopeCode = $scope->getCode();
            $showScope = sprintf( __("for website '%s'"), $scope->getName() );
        }
        elseif ( $scope instanceof \Magento\Store\Api\Data\StoreInterface ) {
            $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $scopeCode = $scope->getCode();
            $showScope = sprintf( __("for store '%s'"), $scope->getName() );
        }
        else {
            $result->setStatus(3);
            $result->setInfo( sprintf(__("Unknown scope")) );
            return $result;
        }

        $result->setValue( $this->_scopeConfig->getValue($path, $scopeType, $scopeCode) );

        $result->setInfo( sprintf( __("Is %s %s"),
            $this->getShowValue($result->getValue(),gettype($recommended)),
            $showScope) );
        if ($recommended == $result->getValue()) {
            $result->setStatus(0);
        } else {
            $result->setStatus(2);
            $result->setAction( sprintf( __("Switch to %s %s"),
                ucfirst($this->getShowValue($recommended,gettype($recommended))),
                $showScope) );
        }

        return $result;
    }

    protected function getShowValue($value,$type='string') {
        if ('boolean' == $type) {
            $showValue = $value ? __('enabled') : __('disabled');
        } elseif ('string' == $type) {
            $showValue = $value;
        } else {
            $showValue = sprintf( __("Unsupported type: '%s'"), $type );
        }
        return $showValue;
    }
}
