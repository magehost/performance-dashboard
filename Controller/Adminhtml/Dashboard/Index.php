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

namespace MageHost\PerformanceDashboard\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Performance Dashboard Index Controller
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Controller\Adminhtml\Dashboard
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class Index extends Action
{
    /**
     * Controller for the overview page of the Performance Dashboard
     *
     * @var PageFactory
     */
    private $_resultPageFactory;

    /**
     * Constructor
     *
     * @param Context     $context           -
     * @param PageFactory $resultPageFactory -
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the page defined in
     * view/adminhtml/layout/magehost_performance_dashboard_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}
