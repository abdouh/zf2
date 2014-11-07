<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Model\ImageUpload;

class MediaManagerController extends AbstractActionController {

    protected $authservice;

    public function indexAction() {
        $userEmail = $this->getAuthService()->getStorage()->read();
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUserByEmail($userEmail);
        $user_id = $user->id;
        $view = new ViewModel(array(
            'myUploads' => $this->getServiceLocator()->get('ImageUploadTable')->getUploadsByUserId($user_id),
            'location' => $this->getFileUploadLocation(),
        ));
        return $view;
    }

    public function uploadAction() {
        $view = new ViewModel(array('form' => $this->getServiceLocator()->get('UploadForm')));
        return $view;
    }

    public function processAction() {
        $form = $this->getServiceLocator()->get('UploadForm');
        $upload = new ImageUpload();
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
                $exchange_data['thumbnail'] = $this->generateThumbnail($uploadFile['name']);
                $userEmail = $this->getAuthService()->getStorage()->read();
                $userTable = $this->getServiceLocator()->get('UserTable');
                $user = $userTable->getUserByEmail($userEmail);
                $exchange_data['user_id'] = $user->id;
                $upload->exchangeArray($exchange_data);
                $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
                $uploadTable->saveUpload($upload);
                return $this->redirect()->toRoute('users/media', array('action' => 'index'));
            }
        }
        $viewModel = new ViewModel(array('form' => $form));
        $viewModel->setTemplate('users/media/upload');
        return $viewModel;
    }

    public function deleteAction() {
        $uploadId = $this->params()->fromRoute('id');
        $UploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $uploadInfo = $UploadTable->getUpload($uploadId);
        $file = $this->getFileUploadLocation() . $uploadInfo->filename;
        unlink($this->getFileUploadLocation() . '/' . $uploadInfo->filename);
        unlink($this->getFileUploadLocation() . '/' . $uploadInfo->thumbnail);
        $UploadTable->deleteUpload($uploadId);
        return $this->redirect()->toRoute('users/media', array('action' => 'index'));
    }

    public function viewAction() {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $upload = $uploadTable->getUpload($uploadId);
        return new ViewModel(array('upload' => $upload));
    }

    public function showImageAction() {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $upload = $uploadTable->getUpload($uploadId);
        // Fetch Configuration from Module Config
        $uploadPath = $this->getFileUploadLocation();
        if ($this->params()->fromRoute('subaction') == 'thumb') {
            $filename = $uploadPath . "/" . $upload->thumbnail;
        } else {
            $filename = $uploadPath . "/" . $upload->filename;
        }
        $file = file_get_contents($filename);
        // Directly return the Response
        $response = $this->getEvent()->getResponse();
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment;filename="'
            . $upload->filename . '"',
        ));
        $response->setContent($file);
        return $response;
    }

    public function getFileUploadLocation() {
        // Fetch Configuration from Module Config
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['images_location'];
    }

    public function generateThumbnail($imageFileName) {
        $path = $this->getFileUploadLocation();
        $sourceImageFileName = $path . '/' . $imageFileName;
        $thumbnailFileName = 'tn_' . $imageFileName;
        $imageThumb = $this->getServiceLocator()->get('WebinoImageThumb');
        $thumb = $imageThumb->create($sourceImageFileName, $options = array());
        $thumb->resize(75, 75);
        $thumb->save($path . '/' . $thumbnailFileName);
        return $thumbnailFileName;
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('authService');
        }
        return $this->authservice;
    }

}
