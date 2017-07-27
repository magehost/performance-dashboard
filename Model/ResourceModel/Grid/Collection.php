<?php

namespace MageHost\PerformanceDashboard\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Data\Collection
{
    /** @var \Magento\Framework\App\Cache\Frontend\Pool */
    protected $_cacheFrontendPool;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $_scopeConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var \Magento\Framework\App\State */
    protected $_appState;

    /** @var \Magento\Framework\App\Cache\TypeListInterface */
    protected $_cacheTypeList;

    /** @var \Magento\Framework\App\DeploymentConfig */
    protected $_deploymentConfig;

    /** @var \Magento\Framework\Filesystem\DirectoryList */
    protected $_directoryList;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_deploymentConfig = $deploymentConfig;
        $this->_directoryList = $directoryList;
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
        $currentBackendClass = get_class($currentBackend);
        $result->setInfo( sprintf(__('%s'),$currentBackendClass) );
        if ( is_a($currentBackend,'Cm_Cache_Backend_Redis') ) {
            $result->setStatus(0);
        } elseif ( 'Zend_Cache_Backend_File' == $currentBackendClass ) {
            $result->setStatus(2);
            $result->setAction( sprintf( __('%s is slow!'), $currentBackendClass ) . "\n" .
                sprintf( __('Store in Redis using Cm_Cache_Backend_Redis'), $name) );
        } elseif ( is_a($currentBackend,'Cm_Cache_Backend_File') ) {
            $result->setStatus(1);
            $result->setAction( sprintf(__('Store in Redis using Cm_Cache_Backend_Redis'),$name) );
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
        $info = array();
        $action = array();
        foreach ($this->_cacheTypeList->getTypes() as $type) {
            if ( ! $type->getStatus() ) {
                $info[] = sprintf( __('Cache is disabled: %s'), $type->getCacheType() );
                $action[] = sprintf( __("Enable %s cache"), $type->getCacheType() );
            }
        }
        if ( empty($action) ) {
            $result->setInfo( __('All cache is enabled') );
            $result->setStatus(0);
        } else {
            $result->setInfo( implode("\n",$info) );
            $result->setAction( implode("\n",$action) );
            $result->setStatus(2);
        }
        return $result;
    }

    protected function getSessionStorageRow()
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( 'Session Storage' );

        /** @see \Magento\Framework\Session\SaveHandler::__construct() */
        $defaultSaveHandler = ini_get('session.save_handler') ?: \Magento\Framework\Session\SaveHandlerInterface::DEFAULT_HANDLER;
        $saveHandler = $this->_deploymentConfig->get(\Magento\Framework\Session\Config::PARAM_SESSION_SAVE_METHOD, $defaultSaveHandler);

        switch($saveHandler) {
            case 'redis':
            case 'memcache':
            case 'memcached':
                $result->setStatus(0);
                $result->setInfo( sprintf(__('Sessions are saved in %s'),ucfirst($saveHandler)) );
                break;
            case 'files':
                $result->setStatus(2);
                $result->setInfo( sprintf(__('Sessions are saved in %s'),ucfirst($saveHandler)) );
                $result->setAction('Save sessions in Redis or Memcached');
                break;
            default:
                $result->setInfo( sprintf(__('Unknown session save handler: %s'),$saveHandler) );
                $result->setStatus(3);
        }
        return $result;
    }

    protected function getNonCachableTemplatesRow()
    {
        $result = new \Magento\Framework\DataObject;
        $result->setTitle( 'Non Cachable Templates' );

        if ( ! function_exists('shell_exec') ) {
            $result->setInfo( __("Can't use the 'shell_exec' function") );
            $result->setStatus(3);
            return $result;
        }
        $binaries = [
            'find'=>null,
            'xargs'=>null,
            'grep'=>null,
        ];
        foreach( $binaries as $key => $dummy ) {
            $binaries[$key] = trim( shell_exec( sprintf('which %s',escapeshellarg($key)) ) );
            if ( empty($binaries[$key]) || ! is_executable($binaries[$key]) ) {
                $result->setInfo( sprintf( __("Can't execute the '%s' command via 'shell_exec'"), $key) );
                $result->setStatus(3);
                return $result;
            }
        }

        $layoutXmlRegex = '.*/layout/.*\.xml';
        $skipRegex = '.*(vendor/magento/|/checkout_|/catalogsearch_result_).*';
        $findInXml = 'cacheable="false"';
        /** @TODO This is a bit slow, about 7 seconds on my Vagrant box. A pure PHP solution would probably be even slower. */
        $command = sprintf( "%s %s %s -regextype 'egrep' -type f -regex %s -not -regex %s | %s %s -n -e %s",
            $binaries['find'],
            escapeshellarg($this->_directoryList->getPath('app')),
            escapeshellarg($this->_directoryList->getRoot().'/vendor'),
            escapeshellarg($layoutXmlRegex),
            escapeshellarg($skipRegex),
            $binaries['xargs'],
            $binaries['grep'],
            $findInXml);
        $output = shell_exec($command);
        if ( empty($output) ) {
            $result->setInfo('No problems found');
            $result->setStatus(0);
        } else {
            $result->setInfo($output);
            $result->setStatus(2);
        }
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

        $result->setInfo( sprintf( __("%s %s"),
            ucfirst($this->getShowValue($result->getValue(),gettype($recommended))),
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
