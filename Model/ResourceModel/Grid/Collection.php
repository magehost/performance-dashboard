<?php

namespace MageHost\PerformanceDashboard\Model\ResourceModel\Grid;

/**
 * Class Collection
 *
 * Data provider for dashboard grid.
 *
 * @package MageHost\PerformanceDashboard\Model\ResourceModel\Grid
 */
class Collection extends \Magento\Framework\Data\Collection
{
    /** @var \MageHost\PerformanceDashboard\Model\DashboardRowFactory */
    private $rowFactory;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    private $scopeConfig;

    /**
     * Constructor
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \MageHost\PerformanceDashboard\Model\DashboardRowFactory $rowFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \MageHost\PerformanceDashboard\Model\DashboardRowFactory $rowFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->rowFactory = $rowFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        parent::__construct($entityFactory);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            if ($printQuery || $logQuery) {
                $this->logger->debug(
                    sprintf(
                        "%s::%s does not get its data from direct database queries," .
                        "it is gathered from several internal Magento objects and logging.",
                        __CLASS__,
                        __FUNCTION__
                    )
                );
            }

            // http://devdocs.magento.com/guides/v2.0/config-guide/prod/prod_perf-optimize.html
            // Idea: Check if Default Cache + Session + FPC are on different Redis instances
            // Idea: FPC hit / miss percentage
            // Idea: Cache flushes per hour

            $this->addItem($this->rowFactory->create('PhpVersion'));
            $this->addItem($this->rowFactory->create('PhpSettings'));
            $this->addItem($this->rowFactory->create('AppStateMode'));
            $this->addItemsCache();
            $this->addItem($this->rowFactory->create('ComposerAutoloader'));
            $this->addItemsConfig();
            $this->addItem($this->rowFactory->create('AsyncIndexes'));

            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Add Cache / Session related dashboard items
     */
    private function addItemsCache()
    {
        $this->addItem($this->rowFactory->create(
            'CacheStorage',
            [
                'identifier' => 'default',
                'name' => 'Magento Cache',
                'buttons' => 'http://devdocs.magento.com/guides/v2.1/config-guide/redis/redis-pg-cache.html'
            ]
        ));
        if (\Magento\PageCache\Model\Config::BUILT_IN ==
            $this->scopeConfig->getValue('system/full_page_cache/caching_application')) {
            $this->addItem($this->rowFactory->create(
                'CacheStorage',
                [
                    'identifier' => 'page_cache',
                    'name' => 'Full Page Cache',
                    'buttons' => 'http://devdocs.magento.com/guides/v2.1/config-guide/redis/redis-pg-cache.html'
                ]
            ));
        }
        $this->addItem($this->rowFactory->create('CacheEnabled'));
        $this->addItem($this->rowFactory->create('SessionStorage'));
        $this->addItem($this->rowFactory->create('NonCacheableLayouts'));
    }

    /**
     * Add configuration related items
     */
    private function addItemsConfig()
    {
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Full Page Caching Application',
                'path' => 'system/full_page_cache/caching_application',
                'recommended' => \Magento\PageCache\Model\Config::VARNISH,
                'source' => 'Magento\PageCache\Model\System\Config\Source\Application',
                'buttons' => 'http://devdocs.magento.com/guides/v2.0/config-guide/varnish/config-varnish.html'
            ]
        ));
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Enable JavaScript Bundling',
                'path' => 'dev/js/enable_js_bundling',
                'recommended' => true,
                'buttons' => 'http://devdocs.magento.com/guides/v2.1/frontend-dev-guide/themes/js-bundling.html'
            ]
        ));
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Merge JavaScript Files',
                'path' => 'dev/js/merge_files',
                'recommended' => true,
                'buttons' => 'http://devdocs.magento.com/guides/v2.0/config-guide/prod/prod_perf-optimize.html'.
                    '#magento---performance-optimizations'
            ]
        ));
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Minify JavaScript Files',
                'path' => 'dev/js/minify_files',
                'recommended' => true,
                'buttons' => 'http://devdocs.magento.com/guides/v2.0/config-guide/prod/prod_perf-optimize.html'.
                    '#magento---performance-optimizations'
            ]
        ));
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Merge CSS Files',
                'path' => 'dev/css/merge_css_files',
                'recommended' => true,
                'buttons' => 'http://devdocs.magento.com/guides/v2.0/config-guide/prod/prod_perf-optimize.html'.
                    '#magento---performance-optimizations'
            ]
        ));
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Minify CSS Files',
                'path' => 'dev/css/minify_files',
                'recommended' => true,
                'buttons' => 'http://devdocs.magento.com/guides/v2.0/config-guide/prod/prod_perf-optimize.html'.
                    '#magento---performance-optimizations'
            ]
        ));
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Minify HTML',
                'path' => 'dev/template/minify_html',
                'recommended' => true,
                'buttons' => 'http://devdocs.magento.com/guides/v2.0/config-guide/prod/prod_perf-optimize.html'.
                    '#magento---performance-optimizations'
            ]
        ));
        $this->addItem($this->rowFactory->create(
            'ConfigSetting',
            [
                'title' => 'Asynchronous sending of sales emails',
                'path' => 'sales_email/general/async_sending',
                'recommended' => true,
                'buttons' => 'http://devdocs.magento.com/guides/v2.0/config-guide/prod/prod_perf-optimize.html'.
                    '#stores---configuration---sales---sales-emails'
            ]
        ));
    }
}
