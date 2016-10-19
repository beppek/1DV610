<?php

/**
 * Helper class for $_SESSION superglobal
 * Handles common session operations
 */
class Session {

    private $userAgent;

    public function __construct() {

        $this->startSession();
        $this->userAgent = new UserAgent();

    }

    public function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            ini_set( 'session.use_trans_sid', false );
            ini_set( 'session.cookie_httponly', true );
            ini_set( 'session.use_only_cookies', true );
            session_start();
        }
    }

    /**
     * @return boolean true if session is hijacked
     */
    public function isHijacked() {
        if (!$this->userAgent->isSame()) {
            return true;
        }
        return false;
    }

    public function setSessionVariable($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function unsetSessionVariable($sessionVariable) {
        unset($_SESSION[$sessionVariable]);
    }

    public function getSessionVariable($sessionVariable) {
        return $_SESSION[$sessionVariable];
    }

    /**
     * @return boolean true if session is logged in
     */
    public function isLoggedIn() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ) {
            return true;
        }
        return false;
    }

    public function regenerateId() {
        session_regenerate_id();
    }

    public function exists($name) {
        if (isset($_SESSION[$name])) {
            return true;
        }
        return false;
    }

    public function destroy() {
        session_destroy();
    }

}
