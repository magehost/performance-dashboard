<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class CacheEnabled
 *
 * Dashboard row to check if all caches are enabled.
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class CacheEnabled extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\Cache\TypeListInterface */
    private $cacheTypeList;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        array $data = []
    ) {
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle('Cache Enabled');
        $this->buttons[] = [
            'label' => __('Cache Management'),
            'url' => 'adminhtml/cache/index'
        ];
        $this->buttons[] = [
            'url' => '[devdocs-guides]/config-guide/cli/config-cli-subcommands-cache.html' .
                '#config-cli-subcommands-cache-clean-over'
        ];

        foreach ($this->cacheTypeList->getTypes() as $type) {
            if (! $type->getStatus()) {
                $this->problems .= sprintf(__('Cache is disabled: %s')."\n", $type->getCacheType());
                $this->actions .= sprintf(__("Enable %s cache")."\n", $type->getCacheType());
            }
        }
        if (empty($this->actions)) {
            $this->info .= __('All cache is enabled')."\n";
        }

        $this->groupProcess();
    }
}
