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

use InvalidArgumentException;
use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;
use Magento\Config\Model\Config\SourceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigSetting
 *
 * Dashboard rows to check optimal config settings.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class ConfigSetting extends DashboardRow implements DashboardRowInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    /**
     * Constructor.
     *
     * Expects data[] keys 'title', 'path' and 'recommended' to be set
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param SourceFactory         $sourceFactory
     * @param array                 $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        SourceFactory $sourceFactory,
        array $data
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->sourceFactory = $sourceFactory;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $defaultResult = $this->checkConfigSetting($this->getPath(), $this->getRecommended());

        $pathParts = explode('/', $this->getPath());
        if (0 < $defaultResult['status']) {
            $this->warnings .= $defaultResult['info'] . "\n";
            $this->actions .= $defaultResult['action'] . "\n";
            $this->buttons[] = [
                'label' => __('Default Config'),
                'url' => sprintf('adminhtml/system_config/edit/section/%s', $pathParts[0]),
                'url_params' => [ '_fragment'=> sprintf('%s_%s-link', $pathParts[0], $pathParts[1]) ]
            ];
        }
        /**
         * @var WebsiteInterface $website
        */
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteResult = $this->checkConfigSetting($this->getPath(), $this->getRecommended(), $website);
            if ($websiteResult['status'] > $defaultResult['status']) {
                $this->warnings .= $websiteResult['info'] . "\n";
                $this->actions .= $websiteResult['action'] . "\n";
                $this->buttons[] = [
                    'label' => sprintf(__('%s Config'), $website->getName()),
                    'url' => sprintf(
                        'adminhtml/system_config/edit/section/%s/website/%s',
                        $pathParts[0],
                        $website->getId()
                    ),
                    'url_params' => [ '_fragment'=> sprintf('%s_%s-link', $pathParts[0], $pathParts[1]) ]
                ];
            }
            foreach ($this->storeManager->getStores() as $store) {
                if ($store->getWebsiteId() == $website->getId()) {
                    $storeResult = $this->checkConfigSetting($this->getPath(), $this->getRecommended(), $store);
                    if ($storeResult['status'] > $websiteResult['status']) {
                        $this->warnings .= $storeResult['info'] . "\n";
                        $this->actions .= $storeResult['action'] . "\n";
                        $this->buttons[] = [
                            'label' => sprintf(__('%s Config'), $store->getName()),
                            'url' => sprintf(
                                'adminhtml/system_config/edit/section/%s/store/%s',
                                $pathParts[0],
                                $store->getId()
                            ),
                            'url_params' => [ '_fragment'=> sprintf('%s_%s-link', $pathParts[0], $pathParts[1]) ]
                        ];
                    }
                }
            }
        }

        if (empty($this->actions)) {
            $this->info .= $defaultResult['info'] . "\n";
        }
        $this->groupProcess();
    }

    /**
     * Check a config setting for a specific scope
     *
     * @param  string      $path
     * @param  mixed       $recommended
     * @param  string|null $scope       -- null = default scope
     * @return array
     */
    private function checkConfigSetting(
        $path,
        $recommended,
        $scope = null
    ) {
        $result = [];

        if (null === $scope) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeCode = null;
            $showScope = __('in Default Config');
        } elseif ($scope instanceof WebsiteInterface) {
            $scopeType = ScopeInterface::SCOPE_WEBSITE;
            $scopeCode = $scope->getCode();
            $showScope = sprintf(__("for website '%s'"), $scope->getName());
        } elseif ($scope instanceof StoreInterface) {
            $scopeType = ScopeInterface::SCOPE_STORE;
            $scopeCode = $scope->getCode();
            $showScope = sprintf(__("for store '%s'"), $scope->getName());
        } else {
            $result['status'] = self::STATUS_UNKNOWN;
            $result['info'] = sprintf(__("Unknown scope"));
            return $result;
        }

        $result['value'] = $this->scopeConfig->getValue($path, $scopeType, $scopeCode);

        $result['info'] = sprintf(
            __("'%s' %s"),
            ucfirst($this->getShowValue($result['value'], $recommended)),
            $showScope
        );
        if ($recommended == $result['value']) {
            $result['status'] = self::STATUS_OK;
        } else {
            $result['status'] = self::STATUS_WARNING;
            $result['action'] = sprintf(
                __("Switch to '%s' %s"),
                ucfirst($this->getShowValue($recommended, $recommended)),
                $showScope
            );
        }

        return $result;
    }

    /**
     * Format a value to show in frontend
     *
     * @param  mixed $value
     * @param  mixed $recommended
     * @return Phrase|string
     * @throws InvalidArgumentException
     */
    private function getShowValue($value, $recommended)
    {
        if (is_bool($recommended)) {
            $showValue = $value ? __('enabled') : __('disabled');
        } elseif (is_string($recommended) || is_int($recommended)) {
            $showValue = $value;
        } else {
            throw new InvalidArgumentException('Unsupported type of recommended value');
        }
        if ($this->getSource()) {
            $sourceModel = $this->sourceFactory->create($this->getSource());
            $sourceArray = $sourceModel->toOptionArray();
            foreach ($sourceArray as $item) {
                if ($item['value'] == $showValue && !empty($item['label'])) {
                    $showValue = $item['label'];
                    break;
                }
            }
        }
        return $showValue;
    }
}
