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

            // Idea: Check if Default Cache + Session + FPC are on different Redis instances
            // Idea: FPC hit / miss percentage
            // Idea: Cache flushes per hour

            $this->addItem($this->rowFactory->create('AppStateMode'));
            $this->addItem($this->rowFactory->create(
                'CacheStorage',
                [
                    'identifier' => 'default',
                    'name' => 'Magento Cache'
                ]
            ));
            if (\Magento\PageCache\Model\Config::BUILT_IN ==
                $this->scopeConfig->getValue('system/full_page_cache/caching_application')) {
                $this->addItem($this->rowFactory->create(
                    'CacheStorage',
                    [
                        'identifier' => 'page_cache',
                        'name' => 'Full Page Cache'
                    ]
                ));
            }
            $this->addItem($this->rowFactory->create('CacheEnabled'));
            $this->addItem($this->rowFactory->create('SessionStorage'));
            $this->addItem($this->rowFactory->create('NonCacheableLayouts'));
            $this->addItem($this->rowFactory->create('ComposerAutoloader'));
            $this->addItem($this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Full Page Caching Application',
                    'path' => 'system/full_page_cache/caching_application',
                    'recommended' => \Magento\PageCache\Model\Config::VARNISH,
                    'source' => 'Magento\PageCache\Model\System\Config\Source\Application'
                ]
            ));
            $this->addItem($this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Use Flat Catalog Categories',
                    'path' => 'catalog/frontend/flat_catalog_category',
                    'recommended' => true
                ]
            ));
            $this->addItem($this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Use Flat Catalog Products',
                    'path' => 'catalog/frontend/flat_catalog_product',
                    'recommended' => true
                ]
            ));
            $this->addItem($this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Merge JavaScript Files',
                    'path' => 'dev/js/merge_files',
                    'recommended' => true
                ]
            ));
            $this->addItem($this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Minify JavaScript Files',
                    'path' => 'dev/js/minify_files',
                    'recommended' => true
                ]
            ));
            $this->addItem($this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Merge CSS Files',
                    'path' => 'dev/css/merge_css_files',
                    'recommended' => true
                ]
            ));
            $this->addItem($this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Minify CSS Files',
                    'path' => 'dev/css/minify_files',
                    'recommended' => true
                ]
            ));
            $this->addItem($this->rowFactory->create('PhpSettings'));

            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
