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

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class MySQLSettings
 *
 * Dashboard row to check MySQL configuration
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class MySQLSettings extends DashboardRow implements DashboardRowInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;


    /**
     * @var int[]
     */
    private $defaultValues
    = [
        'innodb_buffer_pool_size'   => 134217728,
        'max_connections'           => 150,
        'innodb_thread_concurrency' => 0,
    ];

    /**
     * MySQLSettings constructor.
     *
     * @param  ResourceConnection  $resourceConnection
     * @param  array               $data
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        array $data
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setTitle(__('MySQL Configuration'));
        $this->buttons[] = '[devdocs-guides]/performance-best-practices/software.html' .
            '#mysql';

        $connection = $this->resourceConnection->getConnection();
        $info = '';
        foreach ($this->defaultValues as $key => $value) {
            $currentValue = $connection->fetchRow('SHOW VARIABLES LIKE \'' . $key . '\'');
            if (is_array($currentValue) && isset($currentValue['Value'])) {
                if ($currentValue['Value'] <= $value) {
                    $this->problems .= $key . ' lower than - or equal to the default value: '
                        . $currentValue['Value'];
                    $this->actions .= 'Ask your hosting to tune ' . $key;
                }
                $info .= $key . ' = ' . $currentValue['Value'] . "\n";
            }
        }

        if ($this->problems == '') {
            $this->info = $info;
        }

        $this->groupProcess();
    }
}
