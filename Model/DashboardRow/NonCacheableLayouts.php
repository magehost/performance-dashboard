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

use MageHost\PerformanceDashboard\Logger\Handler;
use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\ReadFactory;

/**
 * Class NonCacheableLayouts
 *
 * Dashboard row to show if non cacheable layouts were detected in the frontend.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class NonCacheableLayouts extends DashboardRow implements DashboardRowInterface
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var Handler
     */
    private $logHandler;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * Constructor.
     *
     * @param DirectoryList $directoryList
     * @param Handler       $logHandler
     * @param ReadFactory   $readFactory
     * @param array         $data
     */
    public function __construct(
        DirectoryList $directoryList,
        Handler $logHandler,
        ReadFactory $readFactory,
        array $data = []
    ) {
        $this->directoryList = $directoryList;
        $this->logHandler = $logHandler;
        $this->readFactory = $readFactory;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle('Non Cacheable Layouts');
        $this->setButtons('[devdocs-guides]/frontend-dev-guide/cache_for_frontdevs.html#cache-over-cacheable');

        $output = '';
        $logFiles = $this->logHandler->getLogFiles();
        $now = time();
        foreach ($logFiles as $logFile) {
            $fileMatches = [];
            preg_match('/-(\d\d\d\d-\d\d-\d\d)\./', $logFile, $fileMatches);
            if (empty($fileMatches[1])) {
                continue;
            }
            $date = $fileMatches[1];
            if ($now - strtotime($date) > 3 * 86400) {
                // Older than 3 days
                continue;
            }
            $output .= $this->processLogFile($date, $logFile);
        }

        if (empty($output)) {
            $this->setInfo(__('Collecting data from frontend, no problems found (yet).'));
            $this->setStatus(self::STATUS_OK);
        } else {
            $this->setInfo($output);
            $this->setAction(
                __("Search in frontend layout XML for: cacheable=\"false\"")
            );
            $this->setStatus(self::STATUS_PROBLEM);
        }
        return $this;
    }

    /**
     * Process one logfile
     *
     * @param  $date    string
     * @param  $logFile string
     * @return string
     */
    private function processLogFile($date, $logFile)
    {
        $output = '';
        $moduleCount = [];
        $fileReader = $this->readFactory->create($logFile, DriverPool::FILE);
        $logLines = explode("\n", $fileReader->readAll());
        $fileReader->close();
        foreach ($logLines as $line) {
            $lineMatches = [];
            if (preg_match('/ non_cacheable_layout (\{.+\})(?:\s|$)/', $line, $lineMatches)) {
                $data = json_decode($lineMatches[1], true);
                if (empty($data['Md'])) {
                    continue;
                }
                if (!isset($moduleCount[$data['Md']])) {
                    $moduleCount[$data['Md']] = 0;
                }
                $moduleCount[$data['Md']]++;
            }
        }
        foreach ($moduleCount as $module => $count) {
            $output .= sprintf(__("%s: non cacheable %s page loads:&nbsp;%d\n"), $date, $module, $count);
        }
        return $output;
    }
}
