<?php

/**
 * Helper method to abstract the $_SERVER superglobal
 */
class ServerController {

    private static $requestMethod = 'REQUEST_METHOD';
    private static $phpSelf = 'PHP_SELF';
    private static $location = 'Location: ';
    private static $register = 'register';
    private static $post = 'POST';

    public function requestMethod() {
        return $_SERVER[self::$requestMethod];
    }

    public function requestMethodIsPost() {
        if ($this->requestMethod() == self::$post) {
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
        $hashedVariable = md5($this->getServerVariable($serverVariable));
        return $hashedVariable;
    }

    public function redirectToSelf() {
        header(self::$location . $this->getServerVariable(self::$phpSelf));
    }

}
