<?php

namespace MageHost\PerformanceDashboard\Model;

class DashboardRowFactory
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get DashboardRow Model
     *
     * @param string $instanceName
     * @param array $data
     * @return \Magento\Framework\DataObject
     */
    public function create($instanceName, array $data = [])
    {
        $instanceName = 'MageHost\PerformanceDashboard\Model\DashboardRow\\' . $instanceName;
        $instance = $this->_objectManager->create($instanceName, ['data'=>$data]);
        if (!$instance instanceof \MageHost\PerformanceDashboard\Model\DashboardRowInterface) {
            throw new \UnexpectedValueException("Row class '{$instanceName}' has to be a Dashboard Row.");
        }

        return $instance;
    }
}
