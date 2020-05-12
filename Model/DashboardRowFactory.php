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

namespace MageHost\PerformanceDashboard\Model;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use UnexpectedValueException;

/**
 * Class DashboardRowFactory
 *
 * Factory for dashboard row classes.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class DashboardRowFactory
{
    /**
     * We need the ObjectManager because this is a factory class
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get DashboardRow Model
     *
     * @param  string $instanceName
     * @param  array  $data
     * @return DataObject
     * @throws UnexpectedValueException
     */
    public function create($instanceName, array $data = [])
    {
        $instanceName = 'MageHost\PerformanceDashboard\Model\DashboardRow\\' .
            $instanceName;
        $instance = $this->objectManager->create($instanceName, ['data'=>$data]);
        if (!$instance instanceof DashboardRowInterface) {
            throw new UnexpectedValueException(
                "Row class '{$instanceName}' has to be a Dashboard Row."
            );
        }
        $instance->load();
        return $instance;
    }
}
