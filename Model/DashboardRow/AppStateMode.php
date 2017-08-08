<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class AppStateMode
 *
 * Dashboard row to show Magento Mode: developer / production / default
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class AppStateMode extends \Magento\Framework\DataObject implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\State */
    private $appState;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\State $appState
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        array $data = []
    ) {
    
        $this->appState = $appState;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle('Magento Mode');
        $this->setInfo(sprintf(
            __("Magento is running in '%s' mode"),
            $this->appState->getMode()
        ));
        if (\Magento\Framework\App\State::MODE_PRODUCTION == $this->appState->getMode()) {
            $this->setStatus(0);
        } else {
            $this->setStatus(2);
            $this->setAction(__("Switch mode to Production"));
        }
    }
}
