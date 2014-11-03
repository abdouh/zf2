<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\RegisterForm;
use Users\Form\RegisterFilter;
use Users\Model\Upload;
use Users\Model\UploadTable;

class UploadManagerController extends AbstractActionController {

    private $authservice;

    public function indexAction() {
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $userTable = $this->getServiceLocator()->get('UserTable');
        // Get User Info from Session
        $userEmail = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);
        $viewModel = new ViewModel(array('myUploads' => $uploadTable->getUploadsByUserId($user->id)));
        return $viewModel;
    }

    public function getFileUploadLocation() {
        // Fetch Configuration from Module Config
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }

    public function uploadAction() {
        $form = $this->getServiceLocator()->get('UploadForm');
        $form->bind(new Upload());
        $viewModel = new ViewModel(array('form' => $form));
        return $viewModel;
    }

    public function processAction() {
        $form = $this->getServiceLocator()->get('UploadForm');
        $upload = new Upload();
        //$form->bind($upload);
        $request = $this->getRequest();
        $uploadFile = $this->params()->fromFiles('fileupload');
        $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), array('fileupload' => $uploadFile['name']));
        $form->setData($data);

        if ($form->isValid()) {
            // Fetch Configuration from Module Config
            $uploadPath = $this->getFileUploadLocation();
            // Save Uploaded file
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $adapter->setDestination($uploadPath);

            if ($adapter->receive($uploadFile['name'])) {
                // File upload sucessfull
                $exchange_data = array();
                $exchange_data['label'] = $request->getPost()->get('label');
                $exchange_data['filename'] = $uploadFile['name'];
                $userEmail = $this->getAuthService()->getStorage()->read();
                $userTable = $this->getServiceLocator()->get('UserTable');
                $user = $userTable->getUserByEmail($userEmail);
                $exchange_data['user_id'] = $user->id;
                $upload->exchangeArray($exchange_data);
                $uploadTable = $this->getServiceLocator()->get('UploadTable');
                $uploadTable->saveUpload($upload);
                return $this->redirect()->toRoute('users/upload-manager', array('action' => 'index'));
            }
        }
        $viewModel = new ViewModel(array('form' => $form));
        $viewModel->setTemplate('users/upload-manager/upload');
        return $viewModel;
    }

    public function deleteAction() {
        $uploadId = $this->params()->fromRoute('id');
        $UploadTable = $this->getServiceLocator()->get('UploadTable');
        $uploadInfo = $UploadTable->getUpload($uploadId);
        $file = $this->getFileUploadLocation() . $uploadInfo->filename;
        unlink($this->getFileUploadLocation() . '/' . $uploadInfo->filename);
        $UploadTable->deleteUpload($uploadId);
        return $this->redirect()->toRoute(NULL, array(
                    'controller' => 'UploadManager',
                    'action' => 'index'
                ));
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('authService');
        }
        return $this->authservice;
    }

}
