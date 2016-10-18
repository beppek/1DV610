<?php

require_once('model/Database.php');
require_once('model/Server.php');
require_once('model/Session.php');
require_once('model/PostData.php');

class LoginController {

    private static $sessionMessage = 'message';
    private static $logout = 'LoginView::Logout';
    private static $sessionLoggedIn = 'loggedin';

    private $db;
    private $message;
    private $server;
    private $session;
    private $postData;

    public function __construct() {
        $this->server = new Server();
        $this->session = new Session();
    }

    public function handleRequest() {

		if ($this->server->requestMethodIsPost()) {
			$this->handlePostData();
		} else if ($this->session->exists(self::$sessionMessage)) {
			$this->message = $this->session->getSessionVariable(self::$sessionMessage);
			$this->session->unsetSessionVariable(self::$sessionMessage);
		} else {
			$this->message = '';
		}

    }

    public function handlePostData() {
        $this->postData = new PostData();

        if ($this->postData->postVariableIsSet(self::$logout)) {
            $this->logout();
        } else if (!$this->session->isLoggedIn()) {
            $this->login();
        } else {
            $this->message = '';
        }

    }

    public function getMessage() {
        return $this->message;
    }

    public function login() {

        //TODO: Change $formData arg to only include what is needed and not using strings
        $username = $formData["LoginView::UserName"];
        $password = $formData["LoginView::Password"];

        //TODO: isEmpty helper method
        if (empty($username)) {
            $this->message = 'Username is missing';
        } else if (empty($password)) {
            $this->message = 'Password is missing';
        } else {
            $this->authenticate($formData);
        }

    }

    /**
     * @return string if login fails. Otherwise redirect
     * TODO: Fix return
     */
    public function authenticate($user) {

        //TODO: With better arg to login method string dependency is gone
        $username = $user["LoginView::UserName"];
        $password = $user['LoginView::Password'];

        //TODO: helper method
        if (isset($user["LoginView::KeepMeLoggedIn"]) && $user["LoginView::KeepMeLoggedIn"] === "on") {
            $keep = true;
        } else {
            $keep = false;
        }

        $this->db = new Database();


        if ($this->db->authenticateUser($username, $password)) {
            $_SESSION["username"] = $username;

            //TODO: Helper method
            if ($keep) {
                $cookiePassword = md5(uniqid('', true));
                $this->db->storeCookie($username, $cookiePassword);
                $cookieEndDate = time() + (86400 * 30);
                setcookie("LoginView::CookieName", $username, $cookieEndDate);
                setcookie("LoginView::CookiePassword", $cookiePassword, $cookieEndDate);
                $_SESSION["message"] = "Welcome and you will be remembered";
            } else {
                $_SESSION["message"] = "Welcome";
            }

            //TODO: Session controller class
            $_SESSION["loggedin"] = true;
            session_regenerate_id();

            //TODO: Redirect controller
            return header("Location: " . $_SERVER['PHP_SELF']);
       } else {
            $this->message = "Wrong name or password";
       }

    }

    /**
     * @return string empty if already logged out. Otherwise redirect
     * TODO: Fix return
     */
    public function logout() {

        //TODO: Cookie controller
        if (isset($_COOKIE["username"])) {
            setcookie("LoginView::CookieName", "", time() - 3600);
            setcookie("LoginView::CookiePassword", "", time() - 3600);
        }

        //TODO: Session controller
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            unset($_SESSION["username"]);
            unset($_SESSION["loggedin"]);
            $_SESSION["message"] = "Bye bye!";
            session_regenerate_id();

            //TODO: Redirect controller
            return header("Location: " . $_SERVER['PHP_SELF']);
        } else {
            $this->message = "";
        }
    }

}