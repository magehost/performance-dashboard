<?php
/**
 * Performance Dashboard Extension for Magento 2
 *
 * PHP version 5
 *
 * @category     MageHost
 * @package      MageHost\PerformanceDashboard
 * @author       Jeroen Vermeulen <jeroen@magehost.pro>
 * @copyright    2019 MageHost BV (https://magehost.pro)
 * @license      https://opensource.org/licenses/MIT  MIT License
 * @link         https://github.com/magehost/performance-dashboard
 * @noinspection PhpUndefinedMethodInspection
 */

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Indexer\Model\Indexer\Collection;

/**
 * Class AsyncIndexes
 *
 * Dashboard row to check if Async Indexes are enabled
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class AsyncIndexes extends DashboardRow implements DashboardRowInterface
{
    /**
     * Collection of indexers
     *
     * @var Collection
     */
    private $_indexerCollection;

    /**
     * Magento application config
     *
     * @var ScopeConfigInterface $_scopeConfig
     */
    private $_scopeConfig;

    /**
     * Magento application product metadata
     *
     * @var ProductMetadataInterface $_productMetadata
     */
    private $_productMetadata;

    /**
     * Constructor.
     *
     * @param Collection               $indexerCollection -
     * @param ScopeConfigInterface     $scopeConfig       -
     * @param ProductMetadataInterface $productMetadata   -
     * @param array                    $data              -
     */
    public function __construct(
        Collection $indexerCollection,
        ScopeConfigInterface $scopeConfig,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        $this->_indexerCollection = $indexerCollection;
        $this->_scopeConfig = $scopeConfig;
        $this->_productMetadata = $productMetadata;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     *
     * @return void
     */
    public function load()
    {
        $this->setTitle(__("Asynchronous Indexing"));

        if (version_compare(
            $this->_productMetadata->getVersion(),
            '2.2.0.dev',
            '<'
        )
        ) {
            if (!$this->_scopeConfig->getValue('dev/grid/async_indexing')) {
                $this->warnings .= __("'Asynchronous indexing' is not enabled") .
                    "\n";
                $this->actions .= __("Switch to 'Enabled' in Default Config") .
                    "\n";
                $this->buttons[] = [
                    'label' => __('Default Config'),
                    'url' => 'adminhtml/system_config/edit/section/dev',
                    'url_params' => ['_fragment' => 'dev_grid-link']
                ];
            }
        }

        if (empty($this->warnings)) {
            foreach ($this->_indexerCollection->getItems() as $indexer) {
                if (!$indexer->isScheduled()) {
                    $this->warnings .= sprintf(
                        __("%s Index is set to 'Update on Save'") . "\n",
                        $indexer->getTitle()
                    );
                    $this->actions .= sprintf(
                        __("Switch to 'Update on Schedule'") . "\n",
                        $indexer->getTitle()
                    );
                }
            }
            if ($this->warnings) {
                $this->buttons[] = [
                    'label' => 'Index Management',
                    'url' => 'indexer/indexer/list'
                ];
            }
        }
        $this->buttons[]
            = '[devdocs-guides]/performance-best-practices/configuration.html' .
              '#indexers';

        $this->groupProcess();
    }
}
