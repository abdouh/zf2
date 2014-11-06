<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MediaManagerController extends AbstractActionController {

    public function indexAction() {
        $view = new ViewModel();
        return $view;
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

}