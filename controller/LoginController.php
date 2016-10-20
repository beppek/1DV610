<?php

class LoginController {

    private static $sessionMessage = 'message';
    private static $sessionLoggedIn = 'loggedin';
    private static $sessionUsername = 'username';

    private static $logout = 'LoginView::Logout';
    private static $formUsername = 'LoginView::UserName';
    private static $formPassword = 'LoginView::Password';
    private static $cookieName = 'LoginView::CookieName';
    private static $cookiePassword = 'LoginView::CookiePassword';
    private static $keep = 'LoginView::KeepMeLoggedIn';

    private $db;
    private $server;
    private $session;
    private $cookie;
    private $post;

    private $message;
    private $username;
    private $password;

    public function __construct() {
        $this->server = new ServerController();
        $this->session = new Session();
        $this->cookie = new Cookie();
        $this->db = new Database();
    }

    /**
     * Routes the page request
     * Main access point of class
     * @return void but sets messages to be displayed in view
     */
    public function handleRequest() {

        if ($this->server->requestMethodIsPost()) {
            $this->handleUserData();
        } else if ($this->session->exists(self::$sessionMessage)) {
            $this->message = $this->session->getSessionVariable(self::$sessionMessage);
            $this->session->unsetSessionVariable(self::$sessionMessage);
        } else {
            $this->message = '';
        }

    }

    private function handleUserData() {

        $this->post = new PostData();
        if ($this->post->postVariableIsSet(self::$logout)) {
            $this->logout();
        } else if ($this->session->isLoggedIn() === false) {
            $this->login();
        } else {
            $this->message = '';
        }

    }

    public function getMessage() {
        return $this->message;
    }

    private function login() {

        $this->username = $this->post->getPostDataVariable(self::$formUsername);
        $this->password = $this->post->getPostDataVariable(self::$formPassword);

        if (empty($this->username)) {
            $emptyUsernameMessage = 'Username is missing';
            $this->message = $emptyUsernameMessage;
        } else if (empty($this->password)) {
            $emptyPasswordMessage = 'Password is missing';
            $this->message = $emptyPasswordMessage;
        } else {
            $this->authenticate();
        }

    }

    /**
     * Only call when input has been validated
     */
    private function authenticate() {

        try {
            $isAuthenticated = $this->db->authenticateUser($this->username, $this->password);
        } catch (Exception $e) {
            $failedLoginMessage = 'Oops, database error. Try again later.';
            $this->message = $failedLoginMessage;
            return;
        }

        if ($isAuthenticated) {
            $this->session->setSessionVariable(self::$sessionUsername, $this->username);

            $this->handleKeepLoggedIn();
            $this->session->setSessionVariable(self::$sessionLoggedIn, true);
            $this->session->regenerateId();

            $this->server->redirectToSelf();
        } else {
            $failedLoginMessage = 'Wrong name or password';
            $this->message = $failedLoginMessage;
        }

    }

    private function handleKeepLoggedIn() {

        $welcomeMessage;
        if ($this->shouldStayLoggedIn()) {
            $randomHash = md5(uniqid('', true));
            try {
                $this->db->storeCookie($this->username, $randomHash);
                $this->cookie->set(self::$cookieName, $this->username);
                $this->cookie->set(self::$cookiePassword, $randomHash);
                $welcomeMessage = 'Welcome and you will be remembered';
            } catch (Exception $e) {
                $welcomeMessage = 'Welcome';
            }
        } else {
            $welcomeMessage = 'Welcome';
        }

        $this->session->setSessionVariable(self::$sessionMessage, $welcomeMessage);

    }

    private function shouldStayLoggedIn() {
        if ($this->post->getPostDataVariable(self::$keep) === 'on') {
            return true;
        }
        return false;
    }

    private function logout() {

        $this->cookie->delete(self::$cookieName);
        $this->cookie->delete(self::$cookiePassword);

        if ($this->session->isLoggedIn()) {

            $this->session->unsetSessionVariable(self::$sessionUsername);
            $this->session->unsetSessionVariable(self::$sessionLoggedIn);

            $this->session->setSessionVariable(self::$sessionMessage, 'Bye bye!');
            $this->session->regenerateId();

            $this->server->redirectToSelf();
        } else {
            $this->message = '';
        }
    }

}