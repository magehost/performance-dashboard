<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class NonCacheableLayouts
 *
 * Dashboard row to show if non cacheable layouts were detected in the frontend.
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class NonCacheableLayouts extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\Filesystem\DirectoryList */
    private $directoryList;

    /** @var \MageHost\PerformanceDashboard\Logger\Handler */
    private $logHandler;

    /** @var \\Magento\Framework\Filesystem\File\ReadFactory */
    private $readFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \MageHost\PerformanceDashboard\Logger\Handler $logHandler
     * @param \Magento\Framework\Filesystem\File\ReadFactory $readFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \MageHost\PerformanceDashboard\Logger\Handler $logHandler,
        \Magento\Framework\Filesystem\File\ReadFactory $readFactory,
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
        $this->setButtons('[devdocs-guides]/config-guide/cache/cache-priv-over.html#config-cache-over-cacheable');

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
            if ($now - strtotime($date) > 7 * 86400) {
                // Older than 7 days
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
     * @param $date string
     * @param $logFile string
     * @return string
     */
    private function processLogFile($date, $logFile)
    {
        $output = '';
        $moduleCount = [];
        $fileReader = $this->readFactory->create($logFile, \Magento\Framework\Filesystem\DriverPool::FILE);
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
