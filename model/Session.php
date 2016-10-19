<?php

/**
 * Helper class for $_SESSION superglobal
 * Handles common session operations
 */
class Session {

    private static $userAgent = 'HTTP_USER_AGENT';
    private static $loggedIn = 'loggedin';

    private $server;

    public function __construct() {

        $this->server = new ServerController();
        $this->startSession();

    }

    public function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            ini_set( 'session.use_trans_sid', false );
            ini_set( 'session.cookie_httponly', true );
            ini_set( 'session.use_only_cookies', true );
            session_start();
        }
        $this->setUserAgent();
    }

    private function setUserAgent() {
        if ($this->exists(self::$userAgent) == false) {
            $newUserAgent = $this->server->getHashedVariable(self::$userAgent);
            $this->setSessionVariable(self::$userAgent, $newUserAgent);
        }
    }

    /**
     * @return boolean true if session is hijacked
     */
    public function isHijacked() {
        if ($this->userAgentIsSame()) {
            return false;
        }
        return true;
    }

    public function userAgentIsSame() {
        $storedUserAgent = $this->getSessionVariable(self::$userAgent);
        $hashedRequestingUserAgent = $this->server->getHashedVariable(self::$userAgent);
        if ($storedUserAgent == $hashedRequestingUserAgent) {
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
        if ($this->exists(self::$loggedIn)) {
            if ($this->getSessionVariable(self::$loggedIn) == true) {
                return true;
            }
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
