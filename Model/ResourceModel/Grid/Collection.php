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

namespace MageHost\PerformanceDashboard\Model\ResourceModel\Grid;

use MageHost\PerformanceDashboard\Model\DashboardRowFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\PageCache\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 *
 * Data provider for dashboard grid.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\ResourceModel\Grid
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class Collection extends \Magento\Framework\Data\Collection
{
    /**
     * @var DashboardRowFactory
     */
    private $rowFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Constructor
     *
     * @param EntityFactory            $entityFactory
     * @param DashboardRowFactory      $rowFactory
     * @param ScopeConfigInterface     $scopeConfig
     * @param ProductMetadataInterface $productMetadata
     * @param LoggerInterface          $logger
     */
    public function __construct(
        EntityFactory $entityFactory,
        DashboardRowFactory $rowFactory,
        ScopeConfigInterface $scopeConfig,
        ProductMetadataInterface $productMetadata,
        LoggerInterface $logger
    ) {
        $this->rowFactory = $rowFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->productMetadata = $productMetadata;
        parent::__construct($entityFactory);
    }

    /**
     * Load data
     *
     * @param  bool $printQuery
     * @param  bool $logQuery
     * @return $this
     * @throws \Exception
     * @throws \UnexpectedValueException
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
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addItem($this->rowFactory->create('PhpVersion'));
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addItem($this->rowFactory->create('PhpSettings'));
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addItem($this->rowFactory->create('MySQLSettings'));
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addItem($this->rowFactory->create('AppStateMode'));
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addItem($this->rowFactory->create('HttpVersion'));
            $this->addItemsCache();
            $this->addItemsSearch();
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addItem($this->rowFactory->create('ComposerAutoloader'));
            $this->addItemsConfig();
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addItem($this->rowFactory->create('AsyncIndexes'));
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Add Cache / Session related dashboard items
     *
     * @throws \Exception
     * @throws UnexpectedValueException
     */
    private function addItemsCache()
    {
        $this->addItem(
            $this->rowFactory->create(
                'CacheStorage',
                [
                    'identifier' => 'default',
                    'name' => 'Magento Cache',
                    'buttons' => '[devdocs-guides]/config-guide/redis/redis-pg-cache.html'
                ]
            )
        );
        if (Config::BUILT_IN == $this->scopeConfig->getValue('system/full_page_cache/caching_application')) {
            $this->addItem(
                $this->rowFactory->create(
                    'CacheStorage',
                    [
                        'identifier' => 'page_cache',
                        'name' => 'Full Page Cache',
                        'buttons' => '[devdocs-guides]/config-guide/redis/redis-pg-cache.html'
                    ]
                )
            );
        }
        $this->addItem($this->rowFactory->create('CacheEnabled'));
        $this->addItem($this->rowFactory->create('SessionStorage'));
        $this->addItem($this->rowFactory->create('NonCacheableLayouts'));
    }

    /**
     * Add search related dashboard items
     *
     * @throws \Exception
     * @throws UnexpectedValueException
     */
    private function addItemsSearch()
    {
        if (version_compare($this->productMetadata->getVersion(), '2.3.1', '>=') && version_compare($this->productMetadata->getVersion(), '2.4.0', '<')) {
            $this->addItem(
                $this->rowFactory->create(
                    'ConfigSetting',
                    [
                        'title' => 'Catalog Search Engine',
                        'path' => 'catalog/search/engine',
                        'recommended' => 'elasticsearch6',
                        'source' => \Magento\Search\Model\Adminhtml\System\Config\Source\Engine::class,
                        'buttons' => '[devdocs-guides]/config-guide/elasticsearch/es-overview.html'
                    ]
                )
            );
        }

        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
            $this->addItem(
                $this->rowFactory->create(
                    'ConfigSetting',
                    [
                        'title' => 'Catalog Search Engine',
                        'path' => 'catalog/search/engine',
                        'recommended' => 'elasticsearch7',
                        'source' => \Magento\Search\Model\Adminhtml\System\Config\Source\Engine::class,
                        'buttons' => '[devdocs-guides]/config-guide/elasticsearch/es-overview.html'
                    ]
                )
            );
        }
    }

    /**
     * Add configuration related items
     *
     * @throws \Exception
     * @throws UnexpectedValueException
     */
    private function addItemsConfig()
    {
        $this->addItem(
            $this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Full Page Caching Application',
                    'path' => 'system/full_page_cache/caching_application',
                    'recommended' => Config::VARNISH,
                    'source' => \Magento\PageCache\Model\System\Config\Source\Application::class,
                    'buttons' => '[devdocs-guides]/config-guide/varnish/config-varnish.html'
                ]
            )
        );
        if (version_compare($this->productMetadata->getVersion(), '2.2.0.dev', '<')) {
            if (!$this->runningHttp2()) {
                $this->addItem(
                    $this->rowFactory->create(
                        'ConfigSetting',
                        [
                            'title' => 'Enable JavaScript Bundling',
                            'path' => 'dev/js/enable_js_bundling',
                            'recommended' => true,
                            'buttons' => '[devdocs-guides]/frontend-dev-guide/themes/js-bundling.html'
                        ]
                    )
                );
                $this->addItem(
                    $this->rowFactory->create(
                        'ConfigSetting',
                        [
                            'title' => 'Merge JavaScript Files',
                            'path' => 'dev/js/merge_files',
                            'recommended' => true,
                            'buttons' => '[devdocs-guides]/config-guide/prod/prod_perf-optimize.html' .
                                '#magento---performance-optimizations'
                        ]
                    )
                );
                $this->addItem(
                    $this->rowFactory->create(
                        'ConfigSetting',
                        [
                            'title' => 'Merge CSS Files',
                            'path' => 'dev/css/merge_css_files',
                            'recommended' => true,
                            'buttons' => '[devdocs-guides]/config-guide/prod/prod_perf-optimize.html' .
                                '#magento---performance-optimizations'
                        ]
                    )
                );
            }
            $this->addItem(
                $this->rowFactory->create(
                    'ConfigSetting',
                    [
                        'title' => 'Minify JavaScript Files',
                        'path' => 'dev/js/minify_files',
                        'recommended' => true,
                        'buttons' => '[devdocs-guides]/config-guide/prod/prod_perf-optimize.html' .
                            '#magento---performance-optimizations'
                    ]
                )
            );
            $this->addItem(
                $this->rowFactory->create(
                    'ConfigSetting',
                    [
                        'title' => 'Minify CSS Files',
                        'path' => 'dev/css/minify_files',
                        'recommended' => true,
                        'buttons' => '[devdocs-guides]/config-guide/prod/prod_perf-optimize.html' .
                            '#magento---performance-optimizations'
                    ]
                )
            );
            $this->addItem(
                $this->rowFactory->create(
                    'ConfigSetting',
                    [
                        'title' => 'Minify HTML',
                        'path' => 'dev/template/minify_html',
                        'recommended' => true,
                        'buttons' => '[devdocs-guides]/config-guide/prod/prod_perf-optimize.html' .
                            '#magento---performance-optimizations'
                    ]
                )
            );
        };
        $this->addItem(
            $this->rowFactory->create(
                'ConfigSetting',
                [
                    'title' => 'Asynchronous sending of sales emails',
                    'path' => 'sales_email/general/async_sending',
                    'recommended' => true,
                    'buttons' => '[user-guides]/configuration/sales/sales-emails.html' .
                        '#stores---configuration---sales---sales-emails'
                ]
            )
        );
    }

    private function runningHttp2()
    {
        $httpVersion = $this->rowFactory->create('HttpVersion')->getHttpVersion();
        return (!empty($httpVersion) && version_compare($httpVersion, '2.0', '>='));
    }
}
