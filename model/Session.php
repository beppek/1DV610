<?php

require_once('model/UserAgent.php');

class Session {

    private $userAgent;
    private $isLoggedIn;
    private $message;

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
        if ($this->userAgent->exists()) {
            if ($this->userAgent->isSame()) {
                return false;
            }
            return true;
        }
        $this->userAgent->set();
        return false;
    }

    function setLoggedIn() {

    }

    function unsetLoggedIn() {

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

    function unsetMessage() {

    }

    function regenerateId() {

    }

    function exists() {

    }

}
