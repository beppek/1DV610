<?php

/**
 * Helper method to abstract the $_SERVER superglobal
 */
class ServerController {

    private static $requestMethod = 'REQUEST_METHOD';
    private static $phpSelf = 'PHP_SELF';
    private static $location = 'Location: ';
    private static $register = 'register';

    public function requestMethod() {
        return $_SERVER[self::$requestMethod];
    }

    public function requestMethodIsPost() {
        if ($this->requestMethod() == 'POST') {
            return true;
        }
        return false;
    }

    public function urlParamIsRegister() {
        if (isset($_GET[self::$register])) {
            return true;
        }
        return false;
    }

    public function exists($serverVariable) {
        if (isset($_SERVER[$serverVariable])) {
            return true;
        }
        return false;
    }

    public function getServerVariable($serverVariable) {
        if ($this->exists($serverVariable)) {
            return $_SERVER[$serverVariable];
        }
    }

    public function getHashedVariable($serverVariable) {
        return md5($this->getServerVariable($serverVariable));
    }

    public function redirectToSelf() {
        header(self::$location . $this->getServerVariable(self::$phpSelf));
    }

}
