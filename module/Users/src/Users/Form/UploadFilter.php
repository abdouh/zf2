<?php

namespace Users\Form;

use Zend\InputFilter\InputFilter;

class UploadFilter extends InputFilter {

    public function __construct() {
        $this->add(array(
            'name' => 'fileupload',
            'required' => true,
        ));
        $this->add(array(
            'name' => 'label',
            'required' => true,
        ));
    }

}
