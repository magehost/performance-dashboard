<?php

namespace MageHost\PerformanceDashboard\Block\Backend;

/**
 * Class Dashboard
 * @package MageHost\PerformanceDashboard\Block\Backend
 */
class Dashboard extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @inheritdoc
     *
     * Made 'public' to prevent  phpcs --standard=MEQP2  warning
     */
    public function _construct()
    {
        $this->setData('block_group', 'MageHost_PerformanceDashboard');
        $this->_controller = 'Backend_Dashboard';
        $this->_headerText = __('MageHost Performance Dashboard');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
