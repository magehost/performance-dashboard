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
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * Class CacheEnabled
 *
 * Dashboard row to check if all caches are enabled.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class CacheEnabled extends DashboardRow implements DashboardRowInterface
{
    /**
     * List of Magento application caches
     *
     * @var TypeListInterface
     */
    private $_cacheTypeList;

    /**
     * Constructor.
     *
     * @param TypeListInterface $cacheTypeList -
     * @param array             $data          -
     */
    public function __construct(
        TypeListInterface $cacheTypeList,
        array $data = []
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     *
     * @return void
     */
    public function load()
    {
        $this->setTitle('Cache Enabled');
        $this->buttons[] = [
            'label' => __('Cache Management'),
            'url' => 'adminhtml/cache/index'
        ];
        $this->buttons[] = [
            'url' =>
                '[devdocs-guides]/config-guide/cli/config-cli-subcommands-cache.html'
                . '#config-cli-subcommands-cache-clean-over'
        ];

        foreach ($this->_cacheTypeList->getTypes() as $type) {
            if (! $type->getStatus()) {
                $this->problems .= sprintf(
                    __('Cache is disabled: %s')."\n",
                    $type->getCacheType()
                );
                $this->actions .= sprintf(
                    __("Enable %s cache")."\n",
                    $type->getCacheType()
                );
            }
        }
        if (empty($this->actions)) {
            $this->info .= __('All cache is enabled')."\n";
        }

        $this->groupProcess();
    }
}
