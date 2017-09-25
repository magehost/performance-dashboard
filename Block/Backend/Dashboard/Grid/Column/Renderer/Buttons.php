<?php

namespace MageHost\PerformanceDashboard\Block\Backend\Dashboard\Grid\Column\Renderer;

/**
 * Class Buttons
 * @package MageHost\PerformanceDashboard\Block\Backend\Dashboard\Grid\Column\Renderer
 */
class Buttons extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /** @var \Magento\Framework\App\ProductMetadataInterface */
    private $productMetadata;

    /**
     * Buttons constructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        $this->productMetadata = $productMetadata;
        parent::__construct($context, $data);
    }

    /**
     * Render grid row
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $buttons = $row->getButtons();
        $buttonsHtml = [];
        if (!empty($buttons)) {
            if (!is_array($buttons) || isset($buttons['url'])) {
                $buttons = [$buttons];
            }
            foreach ($buttons as $button) {
                if (is_string($button)) {
                    $button = ['url'=>$button];
                }
                $buttonsHtml[] = $this->getButtonHtml($button);
            }
        }
        return implode("<br />\n", $buttonsHtml);
    }

    /**
     * Get HTML for one button / link
     *
     * @param $button
     * @return string
     */
    private function getButtonHtml($button)
    {
        if (empty($button['url'])) {
            return '';
        }
        $result = '';
        $magentoVersionArray = explode('.', $this->productMetadata->getVersion());
        $button['url'] = str_replace(
            '[devdocs-guides]',
            sprintf('http://devdocs.magento.com/guides/v%d.%d', $magentoVersionArray[0], $magentoVersionArray[1]),
            $button['url']
        );
        if (preg_match('#^https?://#', $button['url'])) {
            $target = empty($button['target']) ? '_blank' : $button['target'];
            if (empty($button['label']) &&
                false !== strpos($button['url'], '//devdocs.magento.com/')) {
                $button['label'] = 'DevDocs';
            }
        } else {
            $routeParams = empty($button['url_params']) ? null : $button['url_params'];
            $button['url'] = $this->_urlBuilder->getUrl($button['url'], $routeParams);
            $target = empty($button['target']) ? '_top' : $button['target'];
        }
        $result .= sprintf('<a href="%s" target="%s">', $button['url'], $target);
        $label = empty($button['label']) ? __('Info') : $button['label'];
        // To show button: <button style="padding: 0.2rem 0.5em; font-size: 1.3rem">%s</button>
        $result .= sprintf('%s', str_replace(' ', '&nbsp;', $label));
        $result .= '</a>';
        return $result;
    }
}
