<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\LoginForm;
use Users\Form\LoginFilter;

class LoginController extends AbstractActionController {

    public function indexAction() {
        $form = new LoginForm();
        $viewModel = new ViewModel(array('form' =>
            $form));
        return $viewModel;
    }

    public function processAction() {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute(NULL, array('controller' => 'login',
                        'action' => 'index'
            ));
        }
        $post = $this->request->getPost();
        $form = new LoginForm();
        $inputFilter = new LoginFilter();
        $form->setInputFilter($inputFilter);
        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => true,
                'form' => $form,
            ));
            $model->setTemplate('users/login/index');
            return $model;
        }
        return $this->redirect()->toRoute(NULL, array(
                    'controller' => 'login',
                    'action' => 'confirm'
        ));
    }

    public function confirmAction() {
        $viewModel = new ViewModel();
        return $viewModel;
    }

}
