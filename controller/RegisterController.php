<?php

require_once("model/Database.php");
require_once('controller/ServerController.php');
require_once('model/Session.php');
require_once('model/PostData.php');
require_once('model/Cookie.php');

class RegisterController {

    private static $formUsername = 'RegisterView::UserName';
    private static $formPassword = 'RegisterView::Password';
    private static $formPasswordRepeat = 'RegisterView::PasswordRepeat';

    private $db;
    private $server;
    private $session;
    private $cookie;
    private $post;

    private $messages;
    private $username;
    private $password;
    private $passwordRepeat;

    public function __construct() {
        $this->server = new ServerController();
        $this->session = new Session();
        $this->cookie = new Cookie();
    }

    public function handleRequest() {

        if ($this->server->requestMethodIsPost()) {
            $this->registerUser();
		} else {
			$this->messages = [];
		}

    }

    public function getMessages() {
        return $this->messages;
    }

    /**
     * Check user input
     * @return array - messages to display on the page
     */
    public function registerUser() {

        $this->post = new PostData();

        $this->username = $this->post->getPostDataVariable(self::$formUsername);
        $this->password = $this->post->getPostDataVariable(self::$formPassword);
        $this->passwordRepeat = $this->post->getPostDataVariable(self::$formPasswordRepeat);

        //TODO: isEmpty, validate and sanitize helper methods
        if (empty($this->username) && empty($this->password)) {
            $this->messages = ['Username has too few characters, at least 3 characters.', 'Password has too few characters, at least 6 characters.'];
        } else if (strlen($this->username) < 3) {
            $this->messages = ['Username has too few characters, at least 3 characters.'];
        } else if (strlen($this->password) < 6) {
            $this->messages = ['Password has too few characters, at least 6 characters.'];
        } else if ($this->password != $this->passwordRepeat){
            $this->messages = ["Passwords do not match."];
        } else if (strlen(strip_tags($this->username)) < strlen($this->username)) {
            $this->messages = ["Username contains invalid characters."];
        } else {
            $this->saveToDB();
        }
        $messages;
    }

    /**
     * Register user.
     * @param $user - should be the $_POST data
     * @return array response from Database->createUser().
     */
    public function saveToDB() {

        $this->db = new Database();

        //TODO: Better variable name
        $res[] = $this->db->createUser($this->username, $this->password);

        //TODO: String dependency.
        if ($res[0] == "Registered new user.") {

            //TODO: Session controller
            $_SESSION["username"] = $this->username;
            $_SESSION["message"] = $res[0];
            session_regenerate_id();

            //TODO: Redirect controller
            header("Location: " . $_SERVER['PHP_SELF']);
        } else {
            $this->messages = $res;
        }

    }

}