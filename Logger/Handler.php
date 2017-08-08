<?php

namespace MageHost\PerformanceDashboard\Logger;

use Monolog\Logger;

class Handler extends \Monolog\Handler\RotatingFileHandler
{
    /** @var \Magento\Framework\Filesystem\DirectoryList */
    private $directoryList;

    /**
     * Logging level
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param int $filename
     * @param int $maxFiles
     * @param bool|int $level
     * @param bool $bubble
     * @param null $filePermission
     * @param bool $useLocking
     * @internal param int $
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        $filename,
        $maxFiles = 0,
        $level = Logger::DEBUG,
        $bubble = true,
        $filePermission = null,
        $useLocking = false
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * @inheritdoc
     */
    public function getTimedFilename()
    {
        if ('/' != substr($this->filename, 0, 1)) {
            // Prepend Magento log dir
            $this->filename = sprintf("%s/%s", $this->directoryList->getPath('log'), $this->filename);
        }
        return parent::getTimedFilename();
    }

    /**
     * @return array
     *
     * Made 'public' to prevent  phpcs --standard=MEQP2  warning
     */
    public function getLogFiles()
    {
        // Using 'glob()' causes  phpcs --standard=MEQP2  warning
        return glob($this->getGlobPattern());
    }
}
