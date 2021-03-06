<?php
/**
 * Zly
 *
 * Theme support for the Zly applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */

namespace ZlyTemplater;

use Zend\Config\Config,
    Zend\Module\Manager,
    Zend\Loader\AutoloaderFactory,
    Zend\EventManager\StaticEventManager;

class Module
{
    protected $plugin;
    protected $view;
    protected $viewListener;

    public function init(Manager $moduleManager)
    {
        $this->initAutoloader();
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'));
    }

    public function initAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        ));
    }

    public function getConfig($env = null)
    {
        return new Config(include __DIR__ . '/configs/module.config.php');
    }
    
    public function initializeView(\Zend\EventManager\Event $e)
    {
        $app          = $e->getParam('application');
        $locator      = $app->getLocator();
        $config       = $e->getParam('config');

        $this->initView($app);
        $viewListener = $this->getViewListener($config->templater, $locator);
        StaticEventManager::getInstance()->attach('Zend\Mvc\Application','route', array($viewListener, 'getLayout'), 1);
        $app->events()->attachAggregate($viewListener);
        $events       = StaticEventManager::getInstance();
        $viewListener->registerStaticListeners($events, $locator);
        $this->initModels($e);
    }
    
    protected function initModels(\Zend\EventManager\Event $e)
    {
        $locator = $e->getParam('application')->getLocator();
        $config = $e->getParam('config');
        $themeModel = $locator->get('ZlyTemplater\Model\Themes');
        $themeModel->setConfig($config->templater);
        $themeModel->setLocator($locator);    
        $layoutModel = $locator->get('ZlyTemplater\Model\Layouts');
        $layoutModel->setConfig($config->templater);
        $layoutModel->setLocator($locator);
    }
    
    protected function getViewListener( $config, $locator)
    {
        if ($this->viewListener instanceof View\Listener) {
            return $this->viewListener;
        }
       
        $viewListener = $locator->get('ZlyTemplater\View\Listener');
        $viewListener->setConfig($config);
        $this->viewListener = $viewListener;
        return $viewListener;
    }

    protected function initView(\Zend\Mvc\Application $app)
    {
        if ($this->view) {
            return $this->view;
        }
        $di     = $app->getLocator();
        $view   = $di->get('view');
        $url    = $view->plugin('url');
        $url->setRouter($app->getRouter());
        $this->view = $view;
        return $view;
    }
    
    public function enable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('templater');
        return true;
    }
    
    public function disable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('templater', false);
        return true;
    }
    
    public function install($values, \Zend\Di\Di $locator)
    {
        $mapModel = $locator->get('ZlyTemplater\Model\Themes');
        $mapModel->initSchema();
        return true;
    }
    
    public function update($values, \Zend\Di\Di $locator)
    {
        $themeModule = $locator->get('ZlyTemplater\Model\Themes');
        $themeModule->updateSchema();
        return true;
    }
    
    public function uninstall($values, \Zend\Di\Di $locator)
    {
        $mapModel = $locator->get('ZlyTemplater\Model\Themes');
        $mapModel->dropSchema();
        return true;
    }
}
