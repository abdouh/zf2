<?php

namespace Users;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Users\Model\User;
use Users\Model\UserTable;
use Users\Model\Upload;
use Users\Model\UploadTable;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mvc\MvcEvent;

class Module {
    /* public function onBootstrap($e) {
      $eventManager = $e->getApplication()->getEventManager();
      $moduleRouteListener = new ModuleRouteListener();
      $moduleRouteListener->attach($eventManager);
      $sharedEventManager = $eventManager->getSharedManager(); // The shared event manager
      $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function($e) {
      $controller = $e->getTarget(); // The controller which is dispatched
      $controllerName = $controller->getEvent()->getRouteMatch()->getParam('controller');
      if (!in_array($controllerName, array('Users\Controller\Index', 'Users\Controller\Register', 'Users\Controller\Login'))) {
      $controller->layout('layout/myaccount');
      }
      });
      } */

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'abstract_factories' => array(),
            'aliases' => array(),
            'factories' => array(
                // DB
                'UserTable' => function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'UploadTable' => function($sm) {
                    $tableGateway = $sm->get('UploadTableGateway');
                    $uploadSharingTableGateway = $sm->get('UploadSharingTableGateway');
                    $table = new UploadTable($tableGateway, $uploadSharingTableGateway);
                    return $table;
                },
                'UploadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Upload());
                    return new TableGateway('uploads', $dbAdapter, null, $resultSetPrototype);
                },
                'UploadSharingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('uploads_sharing', $dbAdapter);
                },
                'ChatMessagesTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Users\Model\ChatMessages());
                    return new TableGateway('chat_messages', $dbAdapter, null, $resultSetPrototype);
                },
                'ImageUploadTable' => function($sm) {
                    $tableGateway = $sm->get('ImageUploadTableGateway');
                    $table = new \Users\Model\ImageUploadTable($tableGateway);
                    return $table;
                },
                'ImageUploadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Users\Model\ImageUpload());
                    return new TableGateway('image_uploads', $dbAdapter, null, $resultSetPrototype);
                },
                // FORMS
                'LoginForm' => function ($sm) {
                    $form = new \Users\Form\LoginForm();
                    $form->setInputFilter($sm->get('LoginFilter'));
                    return $form;
                },
                'RegisterForm' => function ($sm) {
                    $form = new \Users\Form\RegisterForm();
                    $form->setInputFilter($sm->get('RegisterFilter'));
                    return $form;
                },
                'UserEditForm' => function ($sm) {
                    return $sm->get('RegisterForm');
                },
                'UploadForm' => function ($sm) {
                    $form = new \Users\Form\UploadForm();
                    $form->setInputFilter($sm->get('UploadFilter'));
                    return $form;
                },
                // FILTERS
                'LoginFilter' => function ($sm) {
                    return new \Users\Form\LoginFilter();
                },
                'RegisterFilter' => function ($sm) {
                    return new \Users\Form\RegisterFilter();
                },
                'UploadFilter' => function ($sm) {
                    return new \Users\Form\UploadFilter();
                },
                'dbTableAuthAdapter' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user', 'email', 'password', 'MD5(?)');
                    return $dbTableAuthAdapter;
                },
                'authService' => function($sm) {
                    $authService = new AuthenticationService();
                    $authService->setAdapter($sm->get('dbTableAuthAdapter'));
                    return $authService;
                },
            ),
            'invokables' => array(),
            'services' => array(),
            'shared' => array(),
        );
    }

}
