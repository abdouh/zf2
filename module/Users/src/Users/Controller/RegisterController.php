<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\RegisterForm;

class RegisterController extends AbstractActionController {

    public function indexAction() {
        $form = new RegisterForm();
        $viewModel = new ViewModel(array('form' =>
                    $form));
        return $viewModel;
    }

    public function confirmAction() {
        $viewModel = new ViewModel();
        return $viewModel;
    }

}