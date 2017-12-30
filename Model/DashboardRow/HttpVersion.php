<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

/**
 * Class HttpVersion
 *
 * Dashboard row to check HTTP version
 *
 * @package MageHost\PerformanceDashboard\Model\DashboardRow
 */
class HttpVersion extends \MageHost\PerformanceDashboard\Model\DashboardRow implements
    \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\App\RequestInterface */
    private $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        array $data
    ) {
        $this->request = $request;
        parent::__construct($data);
    }

    /**
     * Load Row, is called by DashboardRowFactory
     */
    public function load()
    {
        $this->setTitle(__('HTTP Version'));
        $this->setButtons('https://css-tricks.com/http2-real-world-performance-test-analysis/');

        // We assume if the Admin runs on HTTP/2, the frontend does too.

        $httpVersion = $this->getHttpVersion();
        if (null === $httpVersion) {
            $this->setStatus(self::STATUS_UNKNOWN);
            $this->setInfo(__("Could not check if you are running HTTP/2\n".
                "\$_SERVER['SERVER_PROTOCOL'] may be missing.\n"));
        } elseif (version_compare($httpVersion, '2.0', '>=')) {
            $this->setStatus(self::STATUS_OK);
            $this->setInfo(sprintf(__("HTTP Version: %s\n"), $httpVersion));
        } else {
            $this->setStatus(self::STATUS_PROBLEM);
            $this->setInfo(sprintf(__("Your connection is HTTP %s")."\n", $httpVersion));
            $this->setAction(__("Upgrade to a web server supporting HTTP/2.\n".
                "Check if HTTP/2 is enabled in your server config.\n".
                "Also check if your browser supports HTTP/2.\n"));
        }
    }

    public function getHttpVersion()
    {
        $serverProtocol = $this->request->getServerValue('SERVER_PROTOCOL');
        if (!empty($serverProtocol)) {
            $versionSplit = explode('/', $serverProtocol);
            return $versionSplit[1];
        }
        return null;
    }
}
