<?php

require_once('model/Database.php');
require_once('controller/ServerController.php');
require_once('model/Session.php');
require_once('model/PostData.php');
require_once('model/Cookie.php');

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
    }

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

    public function handleUserData() {
        $this->post = new PostData();
        $this->username = $this->post->getPostDataVariable(self::$formUsername);
        $this->password = $this->post->getPostDataVariable(self::$formPassword);

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

    public function login() {

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

    public function authenticate() {

        $this->db = new Database();

        if ($this->db->authenticateUser($this->username, $this->password)) {

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

    public function handleKeepLoggedIn() {

        $welcomeMessage;
        if ($this->shouldStayLoggedIn()) {
            $randomHash = md5(uniqid('', true));
            $this->db->storeCookie($this->username, $randomHash);
            $this->cookie->set(self::$cookieName, $this->username);
            $this->cookie->set(self::$cookiePassword, $randomHash);
            $welcomeMessage = 'Welcome and you will be remembered';
        } else {
            $welcomeMessage = 'Welcome';
        }

        $this->session->setSessionVariable(self::$sessionMessage, $welcomeMessage);

    }

    public function shouldStayLoggedIn() {
        if ($this->post->getPostDataVariable(self::$keep) === 'on') {
            return true;
        }
        return false;
    }

    public function logout() {

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