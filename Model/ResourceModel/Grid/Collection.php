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

    /**
     * Constructor
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \MageHost\PerformanceDashboard\Model\DashboardRowFactory $rowFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \MageHost\PerformanceDashboard\Model\DashboardRowFactory $rowFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->rowFactory = $rowFactory;
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
        if (!$this->isLoaded()) {
            $this->addItem($this->rowFactory->create('AppStateMode'));
            $this->addItem($this->rowFactory->create(
                'CacheStorage',
                ['identifier' => 'default',
                    'name' => 'Magento Cache']
            ));
            $this->addItem($this->rowFactory->create(
                'CacheStorage',
                ['identifier' => 'page_cache',
                    'name' => 'Full Page Cache']
            ));
            $this->addItem($this->rowFactory->create('CacheEnabled'));
            $this->addItem($this->rowFactory->create('SessionStorage'));
            $this->addItem($this->rowFactory->create('NonCacheableLayouts'));
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
            // Idea: FPC hit / miss percentage
            // Idea: Cache flushes per hour
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
