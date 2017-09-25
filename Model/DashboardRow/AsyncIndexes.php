<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class AsyncIndexes
 *
 * Dashboard row to check if Async Indexes are enabled
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class AsyncIndexes extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Indexer\Model\Indexer\Collection */
    private $indexerCollection;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var \Magento\Framework\App\ProductMetadataInterface $productMetadata */
    private $productMetadata;

    /**
     * Constructor.
     *
     * @param \Magento\Indexer\Model\Indexer\Collection $indexerCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\Indexer\Model\Indexer\Collection $indexerCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        $this->indexerCollection = $indexerCollection;
        $this->scopeConfig = $scopeConfig;
        $this->productMetadata = $productMetadata;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle(__("Asynchronous Indexing"));

        if (version_compare($this->productMetadata->getVersion(), '2.2.0.dev', '<')) {
            if (!$this->scopeConfig->getValue('dev/grid/async_indexing')) {
                $this->warnings .= __("'Asynchronous indexing' is not enabled") . "\n";
                $this->actions .= __("Switch to 'Enabled' in Default Config") . "\n";
                $this->buttons[] = [
                    'label' => __('Default Config'),
                    'url' => 'adminhtml/system_config/edit/section/dev',
                    'url_params' => ['_fragment' => 'dev_grid-link']
                ];
            }
        }

        if (empty($this->warnings)) {
            foreach ($this->indexerCollection->getItems() as $indexer) {
                if (!$indexer->isScheduled()) {
                    $this->warnings .= sprintf(__("%s Index is set to 'Update on Save'") . "\n", $indexer->getTitle());
                    $this->actions .= sprintf(__("Switch to 'Update on Schedule'") . "\n", $indexer->getTitle());
                }
            }
            if ($this->warnings) {
                $this->buttons[] = [
                    'label' => 'Index Management',
                    'url' => 'indexer/indexer/list'
                ];
            }
        }
        $this->buttons[] = '[devdocs-guides]/config-guide/prod/prod_perf-optimize.html'.
            '#magento---performance-optimizations';

        $this->groupProcess();
    }
}
