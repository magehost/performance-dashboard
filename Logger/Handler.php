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

namespace MageHost\PerformanceDashboard\Logger;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Glob;
use Monolog\Handler\RotatingFileHandler;

/**
 * Class Handler
 *
 * Log handler creating rotating logs.
 * We use it to log detected performance problems in the frontend.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Logger
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class Handler extends RotatingFileHandler
{
    /**
     * Application file system directories dictionary.
     *
     * @var DirectoryList
     */
    private $_directoryList;

    /**
     * Constructor
     *
     * @param DirectoryList $directoryList  Application file system directories
     *                                      dictionary.
     * @param string        $filename       @inheritdoc
     * @param int           $maxFiles       @inheritdoc
     * @param int           $level          @inheritdoc
     * @param bool          $bubble         @inheritdoc
     * @param int|null      $filePermission @inheritdoc
     * @param bool          $useLocking     @inheritdoc
     */
    public function __construct(
        DirectoryList $directoryList,
        $filename,
        $maxFiles = 0,
        $level = Logger::DEBUG,
        $bubble = true,
        $filePermission = null,
        $useLocking = false
    ) {
        $this->_directoryList = $directoryList;
        parent::__construct(
            $filename, $maxFiles, $level, $bubble, $filePermission, $useLocking
        );
    }

    /**
     * Get timed filename.
     * Example:  /full/path/to/var/log/mh_noncacheable-2019-10-31.log
     *
     * Running  phpcs --standard=MEQP2  warns because it is protected,
     * like the parent class.
     *
     * @return string
     * @throws FileSystemException
     */
    protected function getTimedFilename()
    {
        if ('/' != substr($this->filename, 0, 1)) {
            // Prepend Magento log dir
            $this->filename = sprintf(
                "%s/%s", $this->_directoryList->getPath('log'),
                $this->filename
            );
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
