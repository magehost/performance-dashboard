<?php

namespace MageHost\PerformanceDashboard\Block\Backend;

/**
 * Class Dashboard
 * @package MageHost\PerformanceDashboard\Block\Backend
 */
class Dashboard extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize object state
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
