<?php

/**
 * Display layout widgets according with block list stored in database
 *
 * @author deeper
 */

namespace Templater\Library\Controller\Action\Helper;

class Widget extends \Zend\Controller\Action\Helper\AbstractHelper {

    /**
     * Blocks action marker
     * @var string
     */
    private $_marker = '_responseSegment';
    /**
     * Application action stack
     * @var Zend_Controller_Plugin_ActionStack
     */
    private $_stack = null;
    static protected $_started = false;
    protected $_incomingWidgetId = null;
    protected $_POST = array();
    protected $_widgetIdMarkerName = null;
    protected $_widgetIdName = '_widgetInternalId';
    protected $_widgetPostMarker = '_widgetId';
    /**
     * Templater options
     * @var array
     */
    protected $_options;
    /**
     * Layout object
     *
     * @var Zend_Layout
     */
    protected $_layout;
    /**
     * @var \Templater\Model\Widgets 
     */
    protected $_model;

    /**
     * Constructor
     * @param array $options
     */
    public function __construct($options = array()) {
        $this->setOptions($options);
        $this->_layoutModel = new \Templater\Model\Layouts();
    }

    /**
     * Set templater options
     * @param array $options
     * @return Templater_Plugin_Layout
     */
    public function setOptions($options = array()) {
        $this->_options = $options;
        return $this;
    }

    /**
     * Get Tempalter options
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    public function init() {
        $this->_front = \Zend\Controller\Front::getInstance();
        if ($this->_front->hasPlugin('Zend\Layout\Controller\Plugin\Layout'))
            $this->_layout = $this->_front
                    ->getPlugin('Zend\Layout\Controller\Plugin\Layout')
                    ->getLayout();
    }

    /**
     * Switch viewRenderer responseSegment to
     * segment received from request
     */
    public function preDispatch() {
        /**
         * Custom action views placed in themes 
         */
        $viewRenderer = $this->getBroker()->load('viewRenderer');
        $module = $this->getRequest()->getModuleName();
        $options = $this->getOptions();
        $viewPath = realpath($this->_layout->getLayoutPath() . '/../' .
                $options['view']['directory'] . '/' . $module . '/scripts/');
        
        $existingPaths = array();
        foreach($this->_layout->getView()->resolver()->getPaths() as $path)
            $existingPaths[] = $path;
        
        if ($viewPath && !in_array($viewPath, $existingPaths)) {
            $this->_layout->getView()->resolver()->addPath($viewPath);
        }

        /**
         * Set widget markers into the new form parameters
         */
        $widgetId = $this->getRequest()->getParam($this->_widgetIdName);
        if ($widgetId !== null) {
            $formHelper = $this->_layout->getView()->getBroker()->load('form');
            if ($formHelper instanceof \Zly\View\Helper\Form)
                $formHelper->setMarker($this->_widgetPostMarker, $widgetId);
        }

        /**
         * Check post parameters
         */
        $postFormMarker = $this->getRequest()->getParam($this->_widgetPostMarker);

        if ($this->getRequest()->isPost()) {
            if ($postFormMarker !== null && $widgetId === null) {
                //id not widget request and post marker found
                $_SERVER['REQUEST_METHOD'] = 'GET';
                $this->_POST = $_POST;
            } elseif ($widgetId !== $postFormMarker) {
                //if post marker NOT of current widget request
                $_SERVER['REQUEST_METHOD'] = 'GET';
                $this->_POST = $_POST;
            }
        }

        if ($postFormMarker !== null && $widgetId == $postFormMarker && !empty($this->_POST)) {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST = $this->_POST;
            $this->_POST = null;
        }

        /**
         * Set view renderer segment of current widget
         */
        $slot = $this->getRequest()->getParam($this->_marker, null);
        $viewRenderer->setResponseSegment($slot);
    }

    /**
     * Find and add application widgets for current request
     */
    public function postDispatch() {

        if (!self::$_started) {
            self::$_started = true;

            $front = $this->_front;
            $layout = $this->_layout;

            if ($layout->isEnabled()) {
                $this->_initStack();

                $apiRequest = new \Zly\Api\Request($this, 'sysmap.currently-active-items');
                $mapIdentifiers = $apiRequest->proceed()->getResponse()->getFirst();

                $request = $this->getActionController()->getRequest();
                $layoutEntity = $this->_layoutModel
                        ->getLayoutWithWidgetsbyNameAndRequest($layout->getLayout(), $mapIdentifiers);

                if(empty($layoutEntity))
                    return false;
                                
                $widgets = $layoutEntity->getWidgets();

                if (!empty($widgets))
                    foreach ($widgets as $widget) {
                        $apiRequest = new \Zly\Api\Request($this, 'sysmap.get-request-by-identifier',
                                        array('identifier' => $widget->getMapId()));
                        $widgetRequest = $apiRequest->proceed()->getResponse()->getFirst();

                        if (!$widgetRequest instanceof \Zend\Controller\Request\AbstractRequest)
                            continue;
                        $this->_pushStack($widget->getId(), $widgetRequest, $widget->getPlaceholder(), (array) $widgetRequest->getParams());
                    }
            }
        }
    }

    /**
     * Push actions into application actions stack
     * @param int $id
     * @param Templater_Api_Interface_Widget $widget
     * @param string $placeholder
     * @param array $params 
     */
    protected function _pushStack($id, \Zend\Controller\Request\AbstractRequest $request, $placeholder, $params = array()) {
        $params[$this->_widgetIdName] = md5($id);
        $camelFilter = new \Zend\Filter\Word\CamelCaseToDash('-');
        $blockRequest = new \Zend\Controller\Request\Simple(
                        strtolower($camelFilter->filter($request->getActionName())),
                        strtolower($camelFilter->filter($request->getControllerName())),
                        strtolower($camelFilter->filter($request->getModuleName())),
                        array_merge($params, array($this->_marker => $placeholder))
        );
        $this->_stack->pushStack($blockRequest);
    }

    /**
     *
     * @return Zend_Controller_Plugin_ActionStack
     */
    protected function _initStack() {
        if (null === $this->_stack) {
            $front = $this->getFrontController();
            if (!$front->hasPlugin('Zend\Controller\Plugin\ActionStack')) {
                $stack = new \Zend\Controller\Plugin\ActionStack();
                $front->registerPlugin($stack);
            } else {
                $stack = $front->getPlugin('ActionStack');
            }
            $this->_stack = $stack;
        }
        return $this->_stack;
    }

}