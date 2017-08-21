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

    /**
     * Constructor.
     *
     * @param \Magento\Indexer\Model\Indexer\Collection $indexerCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Indexer\Model\Indexer\Collection $indexerCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->indexerCollection = $indexerCollection;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle(__("Asynchronous Indexing"));

        if (! $this->scopeConfig->getValue('dev/grid/async_indexing')) {
            $this->warnings .= __("'Asynchronous indexing' is not enabled");
            $this->actions .= __("Switch to 'Enabled' in Default Config");
        } else {
            foreach ($this->indexerCollection->getItems() as $indexer) {
                if (!$indexer->isScheduled()) {
                    $this->warnings .= sprintf(__("%s Index is set to 'Update on Save'") . "\n", $indexer->getTitle());
                    $this->actions .= sprintf(__("Switch to 'Update on Schedule'") . "\n", $indexer->getTitle());
                }
            }
        }

        $this->groupProcess();
    }
}
