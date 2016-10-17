<?php

class Cookie {

    public function set($cookieName, $cookieValue) {
        $cookieEndDate = time() + (86400 * 30);
        setcookie($cookieName, $cookieValue, $cookieEndDate);
    }

    public function unset($cookieName) {
        $expiredDate = time() - 3600;
        setcookie($cookieName, "", $expiredDate);
    }

    public function exists($cookieName) {

    }

}
