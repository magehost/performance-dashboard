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

namespace MageHost\PerformanceDashboard\Block\Backend\Dashboard\Grid\Column;

use MageHost\PerformanceDashboard\Model\DashboardRow;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Statuses
 *
 * Column to show OK / WARNING / PROBLEM / UNKNOWN status in dashboard grid.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Block\Backend\Dashboard\Grid\Column
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class Statuses extends Column
{
    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'decorateStatus'];
    }

    /**
     * Decorate status column values
     *
     * @noinspection                                  PhpUnused
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string        $value    - Column value
     * @param AbstractModel $row      - Grid row
     * @param Column        $column   - Grid column
     * @param bool          $isExport - If exporting
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        // Extra check but mostly to get rid of phpcs warning about unused parameters
        if ($isExport || 'status' != $column->getId()) {
            return $value;
        }
        $cell = htmlentities($value);
        $severity = [
            DashboardRow::STATUS_OK => 'notice',
            DashboardRow::STATUS_WARNING => 'minor',
            DashboardRow::STATUS_PROBLEM => 'critical',
            DashboardRow::STATUS_UNKNOWN => 'minor'
        ];
        if (isset($severity[$row->getStatus()])) {
            $cell = sprintf(
                '<span class="grid-severity-%s"><span>%s</span></span>',
                $severity[$row->getStatus()],
                $cell
            );
        } else {
            $cell = sprintf(__("Unknown status: %s"), json_encode($value));
        }
        return $cell;
    }
}
