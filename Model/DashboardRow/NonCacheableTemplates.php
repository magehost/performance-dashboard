<?php

namespace MageHost\PerformanceDashboard\Model\DashboardRow;

class NonCacheableTemplates extends \Magento\Framework\DataObject implements \MageHost\PerformanceDashboard\Model\DashboardRowInterface
{
    /** @var \Magento\Framework\Filesystem\DirectoryList */
    protected $_directoryList;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        array $data = []
    ) {
        $this->_directoryList = $directoryList;
        parent::__construct($data);

        $this->setTitle('Non Cacheable Templates');

        if (! function_exists('shell_exec')) {
            $this->setInfo(__("Can't use the 'shell_exec' function"));
            $this->setStatus(3);
            return $this;
        }
        $binaries = [
            'find'=>null,
            'xargs'=>null,
            'grep'=>null,
        ];
        foreach (array_keys($binaries) as $key) {
            $binaries[$key] = trim(shell_exec(sprintf('which %s', escapeshellarg($key))));
            if (empty($binaries[$key]) || ! is_executable($binaries[$key])) {
                $this->setInfo(sprintf(__("Can't execute the '%s' command via 'shell_exec'"), $key));
                $this->setStatus(3);
                return $this;
            }
        }

        $layoutXmlRegex = '.*/layout/.*\.xml';
        $skipRegex = '.*(vendor/magento/|/checkout_|/catalogsearch_result_).*';
        $findInXml = 'cacheable="false"';
        /** @TODO This is a bit slow, about 7 seconds on my Vagrant box. A pure PHP solution would probably be even slower. */
        $command = sprintf(
            "%s %s %s -regextype 'egrep' -type f -regex %s -not -regex %s | %s %s -n -e %s",
            $binaries['find'],
            escapeshellarg($this->_directoryList->getPath('app')),
            escapeshellarg($this->_directoryList->getRoot().'/vendor'),
            escapeshellarg($layoutXmlRegex),
            escapeshellarg($skipRegex),
            $binaries['xargs'],
            $binaries['grep'],
            $findInXml
        );
        $output = shell_exec($command);
        if (empty($output)) {
            $this->setInfo('No problems found');
            $this->setStatus(0);
        } else {
            $this->setInfo($output);
            $this->setStatus(2);
        }
        return $this;
    }
}
