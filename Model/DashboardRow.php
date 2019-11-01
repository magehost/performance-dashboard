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

namespace MageHost\PerformanceDashboard\Model;

use Magento\Framework\DataObject;

/**
 * Class DashboardRow
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
abstract class DashboardRow extends DataObject implements
    DashboardRowInterface
{
    const STATUS_OK = 0;
    const STATUS_WARNING = 1;
    const STATUS_PROBLEM = 2;
    const STATUS_UNKNOWN = 3;

    public $problems = '';
    public $warnings = '';
    public $info = '';
    public $actions = '';
    public $buttons = [];

    public function groupProcess()
    {
        if ($this->problems) {
            $this->setStatus(self::STATUS_PROBLEM);
        } elseif ($this->warnings) {
            $this->setStatus(self::STATUS_WARNING);
        } else {
            $this->setStatus(self::STATUS_OK);
        };
        $this->setInfo($this->problems . $this->warnings . $this->info);
        $this->setAction($this->actions);
        if ($this->getButtons()) {
            $existingButtons = $this->getButtons();
            if (!is_array($existingButtons) || isset($existingButtons['url'])) {
                $existingButtons = [$existingButtons];
            }
            $this->buttons = array_merge($this->buttons, $existingButtons);
        }
        $this->setButtons($this->buttons);
    }
}
