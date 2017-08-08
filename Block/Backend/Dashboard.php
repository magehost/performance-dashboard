<?php

namespace MageHost\PerformanceDashboard\Block\Backend;

/**
 * Class Dashboard
 *
 * Container for the dashboard status grid.
 *
 * @package MageHost\PerformanceDashboard\Block\Backend
 */
class Dashboard extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @inheritdoc
     *
     * phpcs --standard=MEQP2  warns because it is protected, like parent class.
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
