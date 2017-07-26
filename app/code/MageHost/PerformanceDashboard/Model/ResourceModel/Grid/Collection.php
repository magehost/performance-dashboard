<?php

namespace MageHost\PerformanceDashboard\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Data\Collection
{
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory
    ) {
        parent::__construct($entityFactory);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $type1 = new \Magento\Framework\DataObject(
                [
                    'id' => 1,
                    'optimalisation' => 'Cache Storage',
                    'description' => 'Where the Magento Cache is stored',
                    'status' => 0,
                ]
            );
            $this->addItem($type1);

            $type2 = new \Magento\Framework\DataObject(
                [
                    'id' => 2,
                    'optimalisation' => 'Session Storage',
                    'description' => 'Where the sessions are stored',
                    'status' => 1,
                ]
            );
            $this->addItem($type2);

            $type3 = new \Magento\Framework\DataObject(
                [
                    'id' => 3,
                    'optimalisation' => 'Non Cachable Templates',
                    'description' => 'Stuff preventing Full Page Cache',
                    'status' => 2,
                ]
            );
            $this->addItem($type3);

            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
