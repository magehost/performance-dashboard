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
            $filename,
            $maxFiles,
            $level,
            $bubble,
            $filePermission,
            $useLocking
        );
    }

    /**
     * Receive currently stored log files
     *
     * @return array
     */
    public function getLogFiles()
    {
        if (strpos($this->filename, $this->_directoryList->getPath('log')) === false) {
            // Fix dir location
            $this->filename = sprintf(
                "%s/%s",
                $this->_directoryList->getPath('log'),
                basename($this->filename)
            );
        }
        return Glob::glob($this->getGlobPattern());
    }
}
