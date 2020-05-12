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

namespace MageHost\PerformanceDashboard\Block\Backend\Dashboard\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObject;

/**
 * Class Buttons
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Block\Backend\Dashboard\Grid\Column\Renderer
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class Buttons extends AbstractRenderer
{
    /**
     * Magento application product metadata
     *
     * @var ProductMetadataInterface
     */
    private $_productMetadata;

    /**
     * Buttons constructor.
     *
     * @param ProductMetadataInterface $productMetadata -
     * @param Context                  $context         -
     * @param array                    $data            -
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        Context $context,
        array $data = []
    ) {
        $this->_productMetadata = $productMetadata;
        parent::__construct($context, $data);
    }

    /**
     * Render grid row
     *
     * @param DataObject $row The row which needs to be rendered.
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        /** @noinspection PhpUndefinedMethodInspection */
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
                $buttonsHtml[] = $this->_getButtonHtml($button);
            }
        }
        return implode("<br />\n", $buttonsHtml);
    }

    /**
     * Get HTML for one button / link
     *
     * @param array $button Array with data about the button
     *
     * @return string
     */
    private function _getButtonHtml($button)
    {
        if (empty($button['url'])) {
            return '';
        }
        $result = '';
        $magentoVersionArray = explode(
            '.',
            $this->_productMetadata->getVersion()
        );
        $button['url'] = str_replace(
            '[devdocs-guides]',
            sprintf(
                'http://devdocs.magento.com/guides/v%d.%d',
                $magentoVersionArray[0],
                $magentoVersionArray[1]
            ),
            $button['url']
        );
        $button['url'] = str_replace(
            '[user-guides]',
            sprintf(
                'https://docs.magento.com/%s/%s/user_guide',
                'm2',
                'ce'
            ),
            $button['url']
        );
        if (preg_match('#^https?://#', $button['url'])) {
            $target = empty($button['target']) ? '_blank' : $button['target'];
            if (empty($button['label'])
                && false !== strpos($button['url'], '//devdocs.magento.com/')
            ) {
                $button['label'] = 'DevDocs';
            }
        } else {
            $routeParams = empty($button['url_params']) ?
                null : $button['url_params'];
            $button['url'] = $this->_urlBuilder->getUrl(
                $button['url'],
                $routeParams
            );
            $target = empty($button['target']) ? '_top' : $button['target'];
        }
        $result .= sprintf('<a href="%s" target="%s">', $button['url'], $target);
        $label = empty($button['label']) ? __('Info') : $button['label'];
        // To show button:
        // <button style="padding: 0.2rem 0.5em; font-size: 1.3rem">%s</button>
        $result .= sprintf(
            '%s',
            str_replace(' ', '&nbsp;', $label)
        );
        $result .= '</a>';
        return $result;
    }
}
