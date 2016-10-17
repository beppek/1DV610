<?php

require_once('model/UserAgent.php');

class Session {

    private $userAgent;

    function __construct() {
        ini_set( 'session.use_trans_sid', false );
        ini_set( 'session.cookie_httponly', true );
        ini_set( 'session.use_only_cookies', true );

        session_start();

        $this->userAgent = new UserAgent();

    }

    function isHijacked() {
        if ($this->userAgent->isSame()) {
            return false;
        }
        return true;
    }

}
