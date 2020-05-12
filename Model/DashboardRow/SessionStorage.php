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

use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Session\Config;
use Magento\Framework\Session\SaveHandlerInterface;

/**
 * Class SessionStorage
 *
 * Dashboard row to check if optimal session storage is used.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class SessionStorage extends DashboardRow implements DashboardRowInterface
{
    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * Constructor
     *
     * @param DeploymentConfig $deploymentConfig
     * @param array            $data
     */
    public function __construct(
        DeploymentConfig $deploymentConfig,
        array $data = []
    ) {
        $this->deploymentConfig = $deploymentConfig;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle('Session Storage');
        $this->setButtons('[devdocs-guides]/config-guide/redis/redis-session.html');

        /**
         * @see \Magento\Framework\Session\SaveHandler::__construct()
         */
        $defaultSaveHandler = ini_get('session.save_handler') ?:
            SaveHandlerInterface::DEFAULT_HANDLER;
        $saveHandler = $this->deploymentConfig->get(
            Config::PARAM_SESSION_SAVE_METHOD,
            $defaultSaveHandler
        );

        switch ($saveHandler) {
            case 'redis':
            case 'memcache':
            case 'memcached':
                $this->setStatus(self::STATUS_OK);
                $this->setInfo(sprintf(__('Sessions are saved in %s'), ucfirst($saveHandler)));
                break;
            case 'files':
            case 'db':
                $this->setStatus(self::STATUS_PROBLEM);
                $this->setInfo(sprintf(__('Sessions are saved in %s'), ucfirst($saveHandler)));
                $this->setAction('Save sessions in Redis or Memcached');
                break;
            default:
                $this->setInfo(sprintf(__('Unknown session save handler: %s'), $saveHandler));
                $this->setStatus(self::STATUS_UNKNOWN);
        }
    }
}
