<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\RegisterForm;
use Users\Form\RegisterFilter;
use Users\Model\User;
use Users\Model\UserTable;

class UserManagerController extends AbstractActionController {

    public function indexAction() {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $viewModel = new ViewModel(array('users' => $userTable->fetchAll()));
        return $viewModel;
    }

    public function editAction() {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUser($this->params()->fromRoute('id'));
        $form = $this->getServiceLocator()->get('UserEditForm');
        $form->bind($user);
        $viewModel = new ViewModel(array(
            'form' => $form,
            'user_id' => $this->params()->fromRoute('id')
        ));
        return $viewModel;
    }

    public function processAction() {
        // Get User ID from POST
        $post = $this->request->getPost();
        $userTable = $this->getServiceLocator()->get('UserTable');
        // Load User entity
        $user = $userTable->getUser($post->id);
        // Bind User entity to Form
        $form = $this->getServiceLocator()->get('UserEditForm');
        $form->bind($user);
        $form->setData($post);
        // Save user
        $this->getServiceLocator()->get('UserTable')->saveUser($user);
        return $this->redirect()->toRoute(NULL, array(
                    'controller' => 'UserManager',
                    'action' => 'index'
        ));
    }

    public function deleteAction() {
        $this->getServiceLocator()->get('UserTable')->deleteUser($this->params()->fromRoute('id'));
        return $this->redirect()->toRoute(NULL, array(
                    'controller' => 'UserManager',
                    'action' => 'index'
        ));
    }

}
