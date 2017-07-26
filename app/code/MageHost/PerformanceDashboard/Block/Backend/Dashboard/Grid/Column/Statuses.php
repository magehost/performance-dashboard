<?php

namespace MageHost\PerformanceDashboard\Block\Backend\Dashboard\Grid\Column;

class Statuses extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'decorateStatus'];
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $cell = htmlentities($value);
        $severity = [0 => 'notice', 1 => 'minor', 2 => 'critical', 3 => 'minor' ];
        if ( isset($severity[$row->getStatus()]) ) {
            $cell = sprintf( '<span class="grid-severity-%s"><span>%s</span></span>',
                             $severity[$row->getStatus()],
                             $cell );
        } else {
            $cell = sprintf( __("Unknown status: %s"), json_encode($value) );
        }
        return $cell;
    }
}
