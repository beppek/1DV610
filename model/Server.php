<?php

class Server {

    public function requestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function requestMethodIsPost() {
        if ($this->requestMethod() == "POST") {
            return true;
        }
        return false;
    }

}
