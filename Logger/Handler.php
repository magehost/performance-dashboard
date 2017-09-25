<?php

namespace MageHost\PerformanceDashboard\Logger;

use Magento\Framework\Filesystem\Glob;

/**
 * Class Handler
 *
 * Log handler creating rotating logs.
 * We use it to log detected performance problems in the frontend.
 *
 * @package MageHost\PerformanceDashboard\Logger
 */
class Handler extends \Monolog\Handler\RotatingFileHandler
{
    /** @var \Magento\Framework\Filesystem\DirectoryList */
    private $directoryList;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @inheritdoc
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        $filename,
        $maxFiles = 0,
        $level = \MageHost\PerformanceDashboard\Logger\Logger::DEBUG,
        $bubble = true,
        $filePermission = null,
        $useLocking = false
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * @inheritdoc
     *
     * phpcs --standard=MEQP2  warns because it is protected, like parent class.
     */
    protected function getTimedFilename()
    {
        if ('/' != substr($this->filename, 0, 1)) {
            // Prepend Magento log dir
            $this->filename = sprintf("%s/%s", $this->directoryList->getPath('log'), $this->filename);
        }
        return parent::getTimedFilename();
    }

    /**
     * Receive currently stored log files
     *
     * @return array
     */
    public function getLogFiles()
    {
        return Glob::glob($this->getGlobPattern());
    }
}
