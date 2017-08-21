<?php

namespace MageHost\PerformanceDashboard\Model;

/**
 * Class DashboardRow
 *
 * @package MageHost\PerformanceDashboard\Model
 */
abstract class DashboardRow extends \Magento\Framework\DataObject implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    const STATUS_OK = 0;
    const STATUS_WARNING = 1;
    const STATUS_PROBLEM = 2;
    const STATUS_UNKNOWN = 3;
}
