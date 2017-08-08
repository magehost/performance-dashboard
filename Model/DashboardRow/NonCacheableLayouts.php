<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class NonCacheableLayouts
 *
 * Dashboard row to show if non cacheable layouts were detected in the frontend.
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class NonCacheableLayouts extends \Magento\Framework\DataObject implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\Filesystem\DirectoryList */
    private $directoryList;

    /** @var \MageHost\PerformanceDashboard\Logger\Handler */
    private $logHandler;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \MageHost\PerformanceDashboard\Logger\Handler $logHandler
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \MageHost\PerformanceDashboard\Logger\Handler $logHandler,
        array $data = []
    ) {
        $this->directoryList = $directoryList;
        $this->logHandler = $logHandler;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle('Non Cacheable Layouts');

        $output = '';
        $logFiles = $this->logHandler->getLogFiles();
        foreach ($logFiles as $logFile) {
            $moduleCount = [];
            $fileMatches = [];
            preg_match('/-(\d\d\d\d-\d\d-\d\d)\./', $logFile, $fileMatches);
            if (empty($fileMatches[1])) {
                continue;
            }
            $date = $fileMatches[1];
            // Using 'file()' causes  phpcs --standard=MEQP2  warning
            $logLines = file($logFile);
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
                $output .= sprintf(__("%s: non cacheable %s page loads: %d\n"), $date, $module, $count);
            }
        }

        if (empty($output)) {
            $this->setInfo(__('Collecting data from frontend, no problems found (yet).'));
            $this->setStatus(0);
        } else {
            $this->setInfo($output);
            $this->setAction(
                __("Search in frontend layout XML for: cacheable=\"false\"")
            );
            $this->setStatus(2);
        }
        return $this;
    }
}
