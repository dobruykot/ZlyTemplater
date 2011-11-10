<?php

namespace Templater\View;

use ArrayAccess,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\EventManager\StaticEventCollection,
    Zend\Http\PhpEnvironment\Response,
    Zend\Http\PhpEnvironment\Response as HttpResponse,
    Zend\Http\PhpEnvironment\Request as HttpRequest,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\EventManager\StaticEventManager,
    Zend\View\Renderer;

class Listener implements ListenerAggregate
{
    protected $layout;
    protected $events;
    protected $listeners = array();
    protected $staticListeners = array();
    protected $view;
    protected $displayExceptions = false;
    
    /**
     * Templater options
     * @var array
     */
    protected $_options;
    /**
     * Layout object
     *
     * @var \Templater\Model\Mapper\Layout
     */
    protected $_layout;
    
    /**
     * @var \Templater\Model\Layouts 
     */
    private $model;
    
    /**
     *
     * @var string 
     */
    protected $themePath;

    /**
     * @var string
     */
    public $config;

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function setView(\Zend\View\Renderer $view)
    {
        $this->view = $view;
    }

    public function setDisplayExceptionsFlag($flag)
    {
        $this->getConfig()->displayExceptions = (bool) $flag;
        return $this;
    }

    public function displayExceptions()
    {
        return $this->getConfig()->display_exceptions;
    }

    public function attach(EventCollection $events)
    {
        $this->events = $events;
        $this->listeners[] = $events->attach('dispatch.error', array($this, 'renderError'));
        $this->listeners[] = $events->attach('dispatch', array($this, 'renderError'), -80);
        $this->listeners[] = $events->attach('dispatch', array($this, 'renderLayout'), -1000);
    }

    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $key => $listener) {
            $events->detach($listener);
            unset($this->listeners[$key]);
            unset($listener);
        }
    }

    public function registerStaticListeners(StaticEventCollection $events, $locator)
    {
        $ident   = 'Zend\Mvc\Controller\ActionController';
        $handler = $events->attach($ident, 'dispatch', array($this, 'renderView'), -50);
        $this->staticListeners[] = array($ident, $handler);
    }

    public function detachStaticListeners(StaticEventCollection $events)
    {
        foreach ($this->staticListeners as $i => $info) {
            list($id, $handler) = $info;
            $events->detach($id, $handler);
            unset($this->staticListeners[$i]);
        }
    }

    public function renderView(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response->isSuccess()) {
            return;
        }

        $routeMatch = $e->getRouteMatch();
        $controller = $routeMatch->getParam('controller', 'index');
        $action     = $routeMatch->getParam('action', 'index');
        $script     = $controller . '/' . $action . '.phtml';

        $vars       = $e->getResult();

        if (is_scalar($vars)) {
            return $vars;
        } elseif (is_object($vars) && !$vars instanceof ArrayAccess) {
            $vars = (array) $vars;
        }

        $content = $this->view->render($script, $vars);

        $e->setResult($content);
        return $content;
    }

    public function renderLayout(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }
        if ($response->isRedirect()) {
            return $response;
        }

        $layout = $this->getLayout($e);


        if (false !== ($contentParam = $e->getParam('content', false))) {
            $vars['content'] = $contentParam;
        } else {
            $vars['content'] = $e->getResult();
        }

        $defaultVars = $this->getConfig()->layout->vars;
        foreach($defaultVars as $var) {
             if (false !== ($contentParam = $layout->getVars($var))) {
                 $content = '';
                 foreach($contentParam as $contentParam){
                      $content .= $contentParam;
                 }
                $vars[$var] = $content;
             }
        }

        $vars['themePath'] = $this->themePath;
        $layout   = $this->view->render($layout->getPath(), $vars);
        $response->setContent($layout);
        return $response;
    }

    public function renderError(MvcEvent $e)
    {
        $error    = $e->getError();
        $app      = $e->getTarget();
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }
        
        if($response->getStatusCode() == 404) {
            $error = $response->getStatusCode();
        }

        if(!empty($error)) {
            switch ($error) {
                case Application::ERROR_CONTROLLER_NOT_FOUND:
                case Application::ERROR_CONTROLLER_INVALID:
                    $vars = array(
                        'message' => 'Page not found.',
                    );
                    $response->setStatusCode(404);
                    break;

                case Application::ERROR_EXCEPTION:
                default:
                    $exception = $e->getParam('exception');
                    $vars = array(
                        'message'            => 'An error occurred during execution, please try again later.',
                        'exception'   => $e->getParam('exception'),
                        'display_exceptions' => $this->displayExceptions(),
                    );
                    $response->setStatusCode(500);
                    break;
            }

            $content = $this->view->render('error/index.phtml', $vars);

            $e->setResult($content);

            return $this->renderLayout($e);
        }

        return '';
    }
    
    public function setModel(\Templater\Model\Layouts $model)
    {
        $this->model = $model;
    }

    /**
     * On dispatch loop startup layout change is happens
     *
     * @param \Zend\Mvc\MvcEvent $event
     * @return \Templater\Model\Mapper\Layout
     */
    public function getLayout(\Zend\Mvc\MvcEvent $event)
    {
        if($this->_layout instanceof \Templater\Model\Mapper\Layout){
             return $this->_layout;
        }

        $request = $event->getRouteMatch();
        $themeSettings = $this->getConfig()->toArray();
        $locator = $event->getTarget()->getLocator();
        
        if($locator->instanceManager()->hasAlias('sysmap-service')) {
            $mapIdentifiers = $locator->get('sysmap-service')
                                    ->getCurentlyActiveItems($request);
            $config = $this->getConfig();

            /**
             * Get current layout from config
             */
            if(empty($mapIdentifiers)) {
                $currentLayout = $this->model->getDefaultLayout();
            } else {
                $currentLayout = $this->model->getCurrentLayout($mapIdentifiers);
            }


            $this->_layout = $currentLayout;
            $this->attachWidgets($event, $currentLayout->getWidgets());

            /* @var $view Zend\View\PhpRenderer */
            $view = $event->getTarget()->getLocator()->get('view');
            $layoutName = '';

        } 
        
        if(!empty($currentLayout)) {
            $themeDirectory = $config->themes->directory .
                    DIRECTORY_SEPARATOR . $currentLayout->getTheme()->getName();
            $layoutPath = $themeDirectory . DIRECTORY_SEPARATOR . $config->layout->directory;

            $view->resolver()->addPath($layoutPath);
            $this->themePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($themeDirectory));
            $layoutName = $currentLayout->getName(). '.phtml';

            $layoutFile = realpath($layoutPath . DIRECTORY_SEPARATOR . $layoutName );
        } else {
            $currentLayout = new \Templater\Model\Mapper\Layout();
        }

        if(!empty($layoutFile)) {
            $currentLayout->setPath($layoutName);
        }
        else {
            $currentLayout->setPath($this->getConfig ()->layout->default);
        }

        return $currentLayout;
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     * @param $widgets
     */
    public function attachWidgets(\Zend\Mvc\MvcEvent $event, $widgets)
    {
        if(!empty($widgets)) {
            $locator = $event->getTarget()->getLocator();
              /* @var $service \Sysmap\Service\Map */
            $service = $locator->get('sysmap-service');
            foreach($widgets as $widget) {
                $routeMatch = $service->getRequestByIdentifier($widget->getMapId());
                if(!empty($routeMatch)) {
                    $widgetEvent = new \Zend\Mvc\MvcEvent();
                    $widgetEvent->setRouteMatch($routeMatch);
                    $widgetEvent->setResponse(new HttpResponse());
                    $controllerName = $routeMatch->getParam('controller');
                    /* @var $controller \Zend\Mvc\Controller\ActionController */
                    $controller = $locator->get($controllerName);
                    $controller->execute($widgetEvent);
                    $content = $this->renderView($widgetEvent);
                    $this->_layout->setVar($widget->getPlaceholder(), $content);
                }
            }
        }
    }
}
