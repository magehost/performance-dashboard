<?php

namespace MageHost\PerformanceDashboard\Model\Layout;

/**
 * Class LayoutPlugin
 *
 * Frontend interceptor to check if a non cacheable layout was used.
 */
class LayoutPlugin
{
    /** @var \Magento\PageCache\Model\Config */
    private $config;

    /** @var \Magento\Framework\Filesystem\DirectoryList */
    private $directoryList;

    /** @var \Magento\Framework\App\RequestInterface */
    private $request;

    /** @var \MageHost\PerformanceDashboard\Logger\Logger */
    private $logger;

    /** @var array */
    private $cacheableModules = ['cms','catalog'];

    const LOG_PREFIX = 'mh_noncacheable';

    /**
     * Constructor.
     *
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \MageHost\PerformanceDashboard\Logger\Logger $logger
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \MageHost\PerformanceDashboard\Logger\Logger $logger
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->directoryList = $directoryList;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\View\Layout $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGenerateXml(\Magento\Framework\View\Layout $subject, $result)
    {
        $module = $this->request->getModuleName();
        if (!$subject->isCacheable() && in_array($module, $this->cacheableModules)) {
            $data = [
                'V' => 1, // Record version, for forward compatibility
                'Md' => $this->request->getModuleName(),
                'Ct' => $this->request->getControllerName(),
                'Ac' => $this->request->getActionName(),
                'Rt' => $this->request->getRouteName()
            ];
            $this->logger->info('non_cacheable_layout', $data);
        }
        return $result;
    }
}
