<?php

require_once('model/UserAgent.php');

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

    function setLoggedIn() {

    }

    function unsetSessionVariable($sessionVariable) {
        unset($_SESSION[$sessionVariable]);
    }

    function isLoggedIn() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ) {
            return true;
        }
        return false;
    }

    function setUserName() {

    }

    function getUserName() {

    }

    function setMessage() {

    }

    function getMessage() {

    }

    function regenerateId() {

    }

    function exists() {

    }

    function destroy() {
        session_destroy();
    }

}
