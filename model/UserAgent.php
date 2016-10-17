<?php

class UserAgent {

    private static $userAgent = 'HTTP_USER_AGENT';

    private $storedUserAgent;
    private $incomingUserAgent;

    function __construct() {
        $this->incomingUserAgent = md5($_SERVER['HTTP_USER_AGENT']);
        $this->set();
    }

    function isSet() {
        if (isset($_SESSION['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    function isSame()
    {
        if ($this->storedUserAgent != $this->incomingUserAgent) {
            return false;
        }
        return true;
    }

    function set() {
        if ($this->isSet()) {
            $this->sessionUserAgent = $_SESSION['HTTP_USER_AGENT'];
        } else {
            $_SESSION['HTTP_USER_AGENT'] = $this->storedUserAgent = $this->incomingUserAgent;
        }
    }
}
