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

use Exception;
use MageHost\PerformanceDashboard\Model\DashboardRow;
use MageHost\PerformanceDashboard\Model\DashboardRowInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class HttpVersion
 *
 * Dashboard row to check HTTP version
 *
 * @category MageHost
 * @package  MageHost\PerformanceDashboard\Model\DashboardRow
 * @author   Jeroen Vermeulen <jeroen@magehost.pro>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     https://github.com/magehost/performance-dashboard
 */
class HttpVersion extends DashboardRow implements DashboardRowInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        array $data
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     *
     * @throws NoSuchEntityException
     */
    public function load()
    {
        $this->setTitle(__('HTTP Version'));
        $this->setButtons('https://css-tricks.com/http2-real-world-performance-test-analysis/');

        $frontUrl = $this->storeManager->getStore()->getBaseUrl('link', false);
        if (preg_match('|^http://|', $frontUrl)) {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setInfo(sprintf(__("Your frontend is not HTTPS")."\n"));
            $this->setAction(
                __(
                    "Update 'Base URL' to use HTTPS.\n".
                    "This is required for HTTP/2\n"
                )
            );
            $this->setButtons(
                [
                'label' => __('Default Config'),
                'url' => 'adminhtml/system_config/edit/section/web',
                'url_params' => [ '_fragment'=> 'web_unsecure-link' ]
                ]
            );
            return;
        }

        $httpVersion = $this->getHttpVersion();
        if (null === $httpVersion) {
            $this->setStatus(self::STATUS_UNKNOWN);
            $this->setInfo(
                __(
                    "Could not check if you are running HTTP/2\n".
                    "\$_SERVER['SERVER_PROTOCOL'] may be missing.\n"
                )
            );
        } elseif (floatval($httpVersion) >= 2) {
            $this->setStatus(self::STATUS_OK);
            $this->setInfo(sprintf(__("HTTP Version: %s\n"), $httpVersion));
        } else {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setInfo(sprintf(__("Your connection is HTTP %s")."\n", $httpVersion));
            $this->setAction(
                __(
                    "Upgrade to a web server supporting HTTP/2.\n".
                    "Check if HTTP/2 is enabled in your server config.\n"
                )
            );
        }
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getHttpVersion()
    {
        // We are looking for HTTP/2 or higher.
        // The $_SERVER['SERVER_PROTOCOL'] is the first place to look.
        // We assume if the current request on the Admin runs on HTTP/2, the frontend does too.

        $serverProtocol = $this->request->getServerValue('SERVER_PROTOCOL');
        if (!empty($serverProtocol)) {
            $versionSplit = explode('/', $serverProtocol);
            $version = $versionSplit[1];
            if (floatval($version) >= 2) {
                return $version;
            }
        }

        // However, the webserver may be behind a reverse proxy.
        // If the reverse proxy is talking HTTP/2 to the client we are still happy.
        // It doesn't matter if the internal connection to the webserver is HTTP/1.

        return $this->getHttpVersionUsingRequest();
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getHttpVersionUsingRequest()
    {
        // We will use Curl to do a HEAD request to the frontend using HTTP/2.
        // This will not work when you are debugging using XDebug because it can't handle 2 requests a the same time.

        $frontUrl = $this->storeManager->getStore()->getBaseUrl();

        try {
            if (!defined('CURL_HTTP_VERSION_2_0')) {
                define('CURL_HTTP_VERSION_2_0', 3);
            }
            // magento-coding-standard discourages use of Curl but it is the best way to check for HTTP/2.
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                [
                CURLOPT_URL => $frontUrl,
                CURLOPT_NOBODY => true,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0, // Enable HTTP/2 in the request
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_CONNECTTIMEOUT => 2, // seconds
                CURLOPT_TIMEOUT => 10 // seconds
                ]
            );
            $httpResponse = curl_exec($curl);
            curl_close($curl);
        } catch (Exception $e) {
            $msg = sprintf("%s: Error fetching '%s': %s", __CLASS__, $frontUrl, $e->getMessage());
            $this->logger->info($msg);
        }

        if (!empty($httpResponse)) {
            $responseHeaders = explode("\r\n", $httpResponse);
            $version = null;
            foreach ($responseHeaders as $header) {
                if (preg_match('|^HTTP/([\d\.]+)|', $header, $matches)) {
                    $version = $matches[1];
                    break;
                }
            }
            if (empty($version) || floatval($version) < 2) {
                foreach ($responseHeaders as $header) {
                    if (preg_match('|^Upgrade: h([\d\.]+)|', $header, $matches)) {
                        $version = $matches[1];
                        break;
                    }
                }
            }
            return $version;
        }

        return null;
    }
}
