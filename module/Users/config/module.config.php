<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Users\Controller\Index' => 'Users\Controller\IndexController',
            'Users\Controller\Register' => 'Users\Controller\RegisterController',
            'Users\Controller\Login' => 'Users\Controller\LoginController',
            'Users\Controller\UserManager' => 'Users\Controller\UserManagerController',
            'Users\Controller\UploadManager' => 'Users\Controller\UploadManagerController',
            'Users\Controller\GroupChat' => 'Users\Controller\GroupChatController',
            'Users\Controller\MediaManager' => 'Users\Controller\MediaManagerController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'users' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/users',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'user-manager' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/user-manager[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',),
                            'defaults' => array(
                                'controller' => 'Users\Controller\UserManager',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'upload-manager' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/upload-manager[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',),
                            'defaults' => array(
                                'controller' => 'Users\Controller\UploadManager',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'group-chat' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/group-chat[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\GroupChat',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'media' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/media[/:action[/:id[/:subaction]]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',
                                'subaction' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\MediaManager',
                                'action' => 'index',
                            ),
                        ),
                    ),
                   /* 'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' =>
                            '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),*/
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'users' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/myaccount' => __DIR__ . '/../view/layout/myaccount-layout.phtml',
        ),
    ),
    'module_config' => array(
        'upload_location' => __DIR__ . '/../data/uploads',
        'images_location' => __DIR__ . '/../data/images',
    ),
);
