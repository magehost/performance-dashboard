<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class ConfigSetting
 *
 * Dashboard rows to check optimal config settings.
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class ConfigSetting extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Config\Model\Config\SourceFactory */
    private $sourceFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Config\Model\Config\SourceFactory $sourceFactory
     * @param array $data -- expects keys 'title', 'path' and 'recommended' to be set
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Config\Model\Config\SourceFactory $sourceFactory,
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
        /** @var \Magento\Store\Api\Data\WebsiteInterface $website */
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
     * @param string $path
     * @param mixed $recommended
     * @param string|null $scope -- null = default scope
     * @return array
     */
    private function checkConfigSetting(
        $path,
        $recommended,
        $scope = null
    ) {
        $result = [];

        if (null === $scope) {
            $scopeType = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeCode = null;
            $showScope = __('in Default Config');
        } elseif ($scope instanceof \Magento\Store\Api\Data\WebsiteInterface) {
            $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
            $scopeCode = $scope->getCode();
            $showScope = sprintf(__("for website '%s'"), $scope->getName());
        } elseif ($scope instanceof \Magento\Store\Api\Data\StoreInterface) {
            $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
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
     * @param mixed $value
     * @param mixed $recommended
     * @return \Magento\Framework\Phrase|string
     * @throws \InvalidArgumentException
     */
    private function getShowValue($value, $recommended)
    {
        if (is_bool($recommended)) {
            $showValue = $value ? __('enabled') : __('disabled');
        } elseif (is_string($recommended) || is_int($recommended)) {
            $showValue = $value;
        } else {
            throw new \InvalidArgumentException('Unsupported type of recommended value');
        }
        if ($this->getSource()) {
            $sourceModel = $this->sourceFactory->create($this->getSource());
            $sourceArray = $sourceModel->toArray();
            if (isset($sourceArray[$showValue])) {
                $showValue = $sourceArray[$showValue];
            }
        }
        return $showValue;
    }
}
