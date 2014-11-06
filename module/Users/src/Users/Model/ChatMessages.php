<?php

namespace Users\Model;

class ChatMessages {

    public $id;
    public $user_id;
    public $message;
    public $stamp;

    function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->message = (isset($data['message'])) ? $data['message'] : null;
        $this->stamp = (isset($data['stamp'])) ? $data['stamp'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
