<?php
/**
 * Performance Dashboard Extension for Magento 2
 *
 * PHP version 5
 *
 * @category     MageHost
 * @package      MageHost\PerformanceDashboard
 * @author       Jeroen Vermeulen <jeroen@magehost.pro>
 * @copyright    2019 MageHost BV (https://magehost.pro)
 * @license      https://opensource.org/licenses/MIT  MIT License
 * @link         https://github.com/magehost/performance-dashboard
 * @noinspection PhpUndefinedMethodInspection
 */

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;
use Magento\Framework\App\State;

/**
 * Class AppStateMode
 *
 * Dashboard row to show Magento Mode: developer / production / default
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class AppStateMode extends DashboardRow implements DashboardRowInterface
{
    /**
     * Application state flags.
     *
     * @var State
     */
    private $_appState;

    /**
     * Constructor.
     *
     * @param State $appState - Application state flags
     * @param array $data     - Data for object
     */
    public function __construct(
        State $appState,
        array $data = []
    ) {
    
        $this->_appState = $appState;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     *
     * @return void
     */
    public function load()
    {
        $appMode = $this->_appState->getMode();
        $this->setTitle('Magento Mode');
        $this->setButtons(
            '[devdocs-guides]/config-guide/cli/config-cli-subcommands-mode.html' .
            '#production-mode'
        );

        $this->setInfo(sprintf(__("Magento is running in '%s' mode"), $appMode));
        if (State::MODE_PRODUCTION == $appMode) {
            $this->setStatus(self::STATUS_OK);
        } else {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setAction(__("Switch to Production Mode"));
        }
    }
}
