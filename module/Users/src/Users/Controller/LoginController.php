<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\LoginForm;
use Users\Form\LoginFilter;

class LoginController extends AbstractActionController {

    public $authservice;

    public function indexAction() {
        $form = new LoginForm();
        $viewModel = new ViewModel(array('form' => $form));
        return $viewModel;
    }

    public function processAction() {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute(NULL, array(
                        'controller' => 'login',
                        'action' => 'index'
                    ));
        }
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('LoginForm');
        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                        'error' => true,
                        'form' => $form,
                    ));
            $model->setTemplate('users/login/index');
            return $model;
        }
        $this->getAuthService()->
                getAdapter()->
                setIdentity($this->request->getPost('email'))->setCredential($this->request->getPost('password'));
        $result = $this->getAuthService()->authenticate();
        if ($result->isValid()) {
            $this->getAuthService()->getStorage()->write($this->request->getPost('email'));
            return $this->redirect()->toRoute(NULL, array(
                        'controller' => 'login',
                        'action' => 'confirm'
                    ));
        }
    }

    public function confirmAction() {
        $user_email = $this->getAuthService()->getStorage()->read();
        $viewModel = new ViewModel(array('user_email' => $user_email));
        return $viewModel;
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('authService');
        }
        return $this->authservice;
    }

}
