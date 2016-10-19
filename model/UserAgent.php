<?php

/**
 * Helper class to check HTTP_USER_AGENT to prevent session hijacking
 */
class UserAgent {

    private static $userAgent = 'HTTP_USER_AGENT';

    private $session;
    private $server;

    public function __construct() {
        $this->session = new Session();
        $this->server = new ServerController();
        $this->set();
    }

    public function exists() {
        if ($this->session->exists(self::$userAgent)) {
            return true;
        } else {
            return false;
        }
    }

    public function isSame() {
        $storedUserAgent = $this->session->getSessionVariable(self::$userAgent);
        $hashedRequestingUserAgent = $this->server->getHashedVariable(self::$userAgent);
        if ($storedUserAgent === $hashedRequestingUserAgent) {
            return true;
        }
        return false;
    }

    public function set() {
        if ($this->exists() === false) {
            $newUserAgent = $this->server->getServerVariable(self::$userAgent);
            $this->session->setSessionVariable(self::$userAgent, $newUserAgent);
        }
    }
}
