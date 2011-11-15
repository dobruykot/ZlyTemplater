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
    'db' => array(
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
                'templater-tools'   => 'ZlyTemplater\Controller\ToolsController',
                'templater-admin'   => 'ZlyTemplater\Controller\AdminController',
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
            'ZlyTemplater\Model\Layouts' => array(
                
            ),
            'ZlyTemplater\Model\Themes' => array(

            ),
            'ZlyTemplater\Form\Theme' => array(
                'parameters' => array(
                    'model' => 'ZlyTemplater\Model\Themes',
                    'view' => 'view'
                )
            ),
            'ZlyTemplater\Form\Layout' => array(
                'parameters' => array(
                    'model' => 'ZlyTemplater\Model\Themes',
                    'view' => 'view'
                )
            ),
            'ZlyTemplater\Form\Widget' => array(
                'parameters' => array(
                    'view' => 'view'
                )
            ),
            'ZlyTemplater\View\Listener' => array(
                'parameters' => array(
                    'model' => 'ZlyTemplater\Model\Layouts',
                    'view' => 'view'
                )
            )
        ),
    ),
);
