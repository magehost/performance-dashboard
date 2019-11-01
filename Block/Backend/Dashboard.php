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

namespace MageHost\PerformanceDashboard\Block\Backend;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Dashboard
 *
 * Container for the dashboard status grid.
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Block\Backend
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class Dashboard extends Container
{
    /**
     * Initialize object state with incoming parameters
     *
     * Running  phpcs --standard=MEQP2  warns because it is protected,
     * like the parent class.
     *
     * Running  phpcs --standard=Magento2  warns because the name is prefixed with an
     * underscore, but this must be the function name for the 'internal constructor',
     * as explained in \Magento\Framework\View\Element\AbstractBlock
     *
     * @noinspection PhpUnused
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setData('block_group', 'MageHost_PerformanceDashboard');
        $this->_controller = 'Backend_Dashboard';
        $this->_headerText = __('MageHost Performance Dashboard');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
