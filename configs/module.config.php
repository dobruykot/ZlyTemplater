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
        'templater-admin' => array(
            'priority' => 99,
            'type'    => 'Zly\Mvc\Router\Http\Route',
            'options' => array(
                'route'    => '/admin/templater/:action/*',
                'defaults' => array(
                    'controller' => 'templater-admin',
                    'action'     => 'index'
                ),
               
            )
        ),
    ),
    'di' => array(
        'instance' => array(
            'alias' => array(
                'view'  => 'Zend\View\PhpRenderer',
                'templater-tools'   => 'ZlyTemplater\Controller\ToolsController',
                'templater-admin'   => 'ZlyTemplater\Controller\AdminController',                
                'templater-cache' => 'Zend\Cache\Storage\Adapter\Filesystem'
            ),
            'templater-cache' => array(
                'injections' => array(
                    'setOptions' => array(
                        'options' => array(
                            'cache_dir'=> __DIR__ . '/../../../data/cache'
                         )
                    )
                )
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
            'ZlyTemplater\Model\Widgets' => array(
                'parameters' => array(
                    'em' => 'readwrite'
                )
            ),
            'ZlyTemplater\Model\Layouts' => array(
                'parameters' => array(
                    'em' => 'readwrite'
                )
            ),
            'ZlyTemplater\Model\Themes' => array(
                'parameters' => array(
                    'em' => 'readwrite'
                )
            ),
            'ZlyTemplater\Form\Theme' => array(
                'parameters' => array(
                    'view' => 'view'
                )
            ),
            'ZlyTemplater\Form\Layout' => array(
                'parameters' => array(
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
