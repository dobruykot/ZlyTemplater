<?php
return array(
    'templater' => array(
        'display_exceptions' => true,
        'themes' => array(
            'directory' => __DIR__.'/../themes'
        ),
        'views' => array(
            'directory' => 'views'
        ),
        'layout' => array(
            'directory' => 'layouts',
            'default' => 'layouts/layout.phtml',
            'admin' => 'admin.phtml',
            'encoding' => 'utf-8',
            'vars' => array(
                'footer',
                'header',
                'left',
                'right',
                'top'
            )
        )
    ),
    'doctrine' => array(
        'entitiesPaths' => array(
            'templater'=>__DIR__ . '/../src/Templater/Model/Mapper',
        )
    ),
    'routes' => array(
 
    ),
    'di' => array(
        'instance' => array(
            'alias' => array(
                'view'  => 'Zend\View\PhpRenderer',
                'templater-tools'   => 'Templater\Controller\ToolsController',
                'templater-admin'   => 'Templater\Controller\AdminController',
            ),
            'Zend\View\HelperBroker' => array(
                'parameters' => array(
                    'loader' => 'Zly\View\HelperLoader',
                ),
            ),
            
            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options'  => array(
                        'script_paths' => array(
                            'templater' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),
            'Templater\Model\Layouts' => array(
                
            ),
            'Templater\Model\Themes' => array(

            ),
            'Templater\Form\Theme' => array(
                'parameters' => array(
                    'model' => 'Templater\Model\Themes',
                    'view' => 'view'
                )
            ),
            'Templater\Form\Layout' => array(
                'parameters' => array(
                    'model' => 'Templater\Model\Themes',
                    'view' => 'view'
                )
            ),
            'Templater\Form\Widget' => array(
                'parameters' => array(
                    'view' => 'view'
                )
            ),
            'Templater\View\Listener' => array(
                'parameters' => array(
                    'model' => 'Templater\Model\Layouts',
                    'view' => 'view'
                )
            )
        ),
    ),
);
