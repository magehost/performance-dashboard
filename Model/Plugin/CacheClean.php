<?php

namespace MageHost\PerformanceDashboard\Model\Plugin;

class CacheClean
{
    /** @var \Magento\Framework\App\Request\Http */
    protected $_request;

    /** @var \Magento\Framework\App\State */
    protected $_state;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\State $state
    ) {
        $this->_request = $request;
        $this->_state = $state;
    }

    /** works on Magento\Framework\App\Cache */
    public function afterLoad(
        $subject, $result
    ) {
        return $result;
    }

    /** works on Magento\Framework\App\Cache */
    public function afterSave(
        $subject, $result
    ) {
        return $result;
    }

    public function afterRemove(
        $subject, $result
    ) {
        return $result;
    }

    /** works on Magento\Framework\App\Cache */
    public function afterClean(
        $subject, $result
    ) {
        return $result;
    }

    /** works on Magento\Framework\App\Cache\TypeList */
    public function afterCleanType(
        $subject, $result
    ) {
        return $result;
    }

    public function afterDelete(
        $subject, $result
    ) {
        return $result;
    }

    /** works on \Magento\Framework\View\Layout */
    public function beforeRenderNonCachedElement($context, $name) {
        if ( \Magento\Framework\App\Area::AREA_FRONTEND == $this->_state->getAreaCode() ) {
            $moduleName = $this->_request->getModuleName();
            $controller = $this->_request->getControllerName();
            $action     = $this->_request->getActionName();
            $route      = $this->_request->getRouteName();
            $nonCachedModules = ['customer','catalogsearch','checkout','sales','contact'];
            if ( !in_array($moduleName,$nonCachedModules) ) {
                error_log( sprintf('RenderNonCachedElement %s %s %s',$moduleName,$action,$name) );
            }
        }
        return [$name];
    }

    public function beforeUniversal($context) {
        $arguments = func_get_args();
        array_shift($arguments);
        return $arguments;
    }
}