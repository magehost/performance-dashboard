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

namespace MageHost\PerformanceDashboard\Model\Layout;

use MageHost\PerformanceDashboard\Logger\Logger;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\View\Layout;
use Magento\PageCache\Model\Config;

/**
 * Class LayoutPlugin
 *
 * Frontend interceptor to check if a non cacheable layout was used.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\Layout
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class LayoutPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $cacheableModules = ['cms','catalog'];

    const LOG_PREFIX = 'mh_noncacheable';

    /**
     * Constructor.
     *
     * @param Config           $config
     * @param RequestInterface $request
     * @param DirectoryList    $directoryList
     * @param Logger           $logger
     */
    public function __construct(
        Config $config,
        RequestInterface $request,
        DirectoryList $directoryList,
        Logger $logger
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->directoryList = $directoryList;
        $this->logger = $logger;
    }

    /**
     * @param  Layout $subject
     * @param  mixed  $result
     * @return mixed
     */
    public function afterGenerateXml(Layout $subject, $result)
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
