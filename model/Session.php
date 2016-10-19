<?php

class Session {

    private $userAgent;

    function __construct() {

        $this->startSession();
        $this->userAgent = new UserAgent();

    }

    function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            ini_set( 'session.use_trans_sid', false );
            ini_set( 'session.cookie_httponly', true );
            ini_set( 'session.use_only_cookies', true );
            session_start();
        }
    }

    function isHijacked() {
        if (!$this->userAgent->isSame()) {
            return true;
        }
        return false;
    }

    function setSessionVariable($name, $value) {
        $_SESSION[$name] = $value;
    }

    function unsetSessionVariable($sessionVariable) {
        unset($_SESSION[$sessionVariable]);
    }

    function getSessionVariable($sessionVariable) {
        return $_SESSION[$sessionVariable];
    }

    function isLoggedIn() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ) {
            return true;
        }
        return false;
    }

    function regenerateId() {
        session_regenerate_id();
    }

    function exists($name) {
        if (isset($_SESSION[$name])) {
            return true;
        }
        return false;
    }

    function destroy() {
        session_destroy();
    }

}
