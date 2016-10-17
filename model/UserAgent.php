<?php

class UserAgent {

    public function __construct() {
        $this->set();
    }

    public function exists() {
        if (isset($_SESSION['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    public function isSame() {
        if ($_SESSION['HTTP_USER_AGENT'] == md5($_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        return false;
    }

    public function set() {
        if (!$this->exists()) {
            $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
        }
    }
}
