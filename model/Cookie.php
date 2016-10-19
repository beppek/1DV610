<?php

/**
 * Helper class to handle cookies
 * Abstracts common cookie operations.
 * Example usage: $cookie->set($cookieName, $cookieValue);
 */
class Cookie {

    /**
     * Sets cookie with expiry date 1 month in the future
     * @param $cookieName string
     * @param $cookieValue - value can be set to any type
     */
    public function set($cookieName, $cookieValue) {
        $cookieEndDate = time() + (86400 * 30);
        setcookie($cookieName, $cookieValue, $cookieEndDate);
    }

    /**
     * Deletes cookie by setting expiry date to 1 hour in the past
     * @param $cookieName string
     */
    public function delete($cookieName) {
        if ($this->exists($cookieName)) {
            $expiredDate = time() - 3600;
            setcookie($cookieName, '', $expiredDate);
        }
    }

    /**
     * @returns boolean true if cookie exists
     */
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