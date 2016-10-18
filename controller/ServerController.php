<?php

class ServerController {

    public function requestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function requestMethodIsPost() {
        if ($this->requestMethod() == "POST") {
            return true;
        }
        return false;
    }

    public function redirectToSelf() {
        header('Location: ' . $_SERVER['PHP_SELF']);
    }

}
