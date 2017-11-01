<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class SessionStorage
 *
 * Dashboard row to check if optimal session storage is used.
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class SessionStorage extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\DeploymentConfig */
    private $deploymentConfig;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
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

        /** @see \Magento\Framework\Session\SaveHandler::__construct() */
        $defaultSaveHandler = ini_get('session.save_handler') ?:
            \Magento\Framework\Session\SaveHandlerInterface::DEFAULT_HANDLER;
        $saveHandler = $this->deploymentConfig->get(
            \Magento\Framework\Session\Config::PARAM_SESSION_SAVE_METHOD,
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
