<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

class CacheEnabled extends \Magento\Framework\DataObject implements \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\Cache\TypeListInterface */
    protected $_cacheTypeList;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        array $data = []
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($data);

        $this->setTitle('Cache Enabled');
        $info = [];
        $action = [];
        foreach ($this->_cacheTypeList->getTypes() as $type) {
            if (! $type->getStatus()) {
                $info[] = sprintf(__('Cache is disabled: %s'), $type->getCacheType());
                $action[] = sprintf(__("Enable %s cache"), $type->getCacheType());
            }
        }
        if (empty($action)) {
            $this->setInfo(__('All cache is enabled'));
            $this->setStatus(0);
        } else {
            $this->setInfo(implode("\n", $info));
            $this->setAction(implode("\n", $action));
            $this->setStatus(2);
        }
    }
}
