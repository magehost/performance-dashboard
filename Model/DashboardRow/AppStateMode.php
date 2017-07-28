<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

class AppStateMode extends \Magento\Framework\DataObject implements \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\State */
    protected $_appState;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\State $appState
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        array $data = []
    )
    {
        $this->_appState = $appState;

        parent::__construct($data);

        $this->setTitle( 'Magento Mode' );
        $this->setInfo( sprintf( __("Magento is running in '%s' mode"),
            $this->_appState->getMode() ) );
        if ( \Magento\Framework\App\State::MODE_PRODUCTION == $this->_appState->getMode() ) {
            $this->setStatus(0);
        } else {
            $this->setStatus(2);
            $this->setAction( __("Switch mode to Production") );
        }
    }
}