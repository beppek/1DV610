<?php

class Cookie {

    public function set($cookieName, $cookieValue) {
        $cookieEndDate = time() + (86400 * 30);
        setcookie($cookieName, $cookieValue, $cookieEndDate);
    }

    public function delete($cookieName) {
        if ($this->exists($cookieName)) {
            $expiredDate = time() - 3600;
            setcookie($cookieName, '', $expiredDate);
        }
    }

    public function exists($cookieName) {
        if (isset($_COOKIE[$cookieName])) {
            return true;
        }
        return false;
    }

    public function getValue($cookieName) {
        if ($this->exists($cookieName)) {
            return $_COOKIE[$cookieName];
        }
    }

}