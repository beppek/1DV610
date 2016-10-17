<?php

class UserAgent {
    private $storedUserAgent;
    private $incomingUserAgent;

    public function __construct() {
        $this->incomingUserAgent = md5($_SERVER['HTTP_USER_AGENT']);
        $this->set();
    }

    private function isSet() {
        if (isset($_SESSION['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    public function isSame() {
        if ($this->storedUserAgent == $this->incomingUserAgent) {
            return true;
        }
        return false;
    }

    private function set() {
        if ($this->isSet()) {
            $this->storedUserAgent = $_SESSION['HTTP_USER_AGENT'];
        } else {
            $_SESSION['HTTP_USER_AGENT'] = $this->storedUserAgent = $this->incomingUserAgent;
        }
    }
}
