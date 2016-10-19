<?php

class RegisterController {

    private static $formUsername = 'RegisterView::UserName';
    private static $formPassword = 'RegisterView::Password';
    private static $formPasswordRepeat = 'RegisterView::PasswordRepeat';
    private static $sessionMessage = 'message';
    private static $sessionUsername = 'username';

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
        $this->messages = [];
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

    private function registerUser() {

        $this->post = new PostData();

        $this->username = $this->post->getPostDataVariable(self::$formUsername);
        $this->password = $this->post->getPostDataVariable(self::$formPassword);
        $this->passwordRepeat = $this->post->getPostDataVariable(self::$formPasswordRepeat);

        $formValidator = new FormValidator();
        $formValidator->validateFormData();

        if ($formValidator->formDataIsValid()) {
            $this->saveToDB();
        } else {
            $this->messages = $formValidator->getErrorMessages();
        }

    }

    private function saveToDB() {

        $this->db = new Database();

        try {

            $this->db->createUser($this->username, $this->password);
            $message = 'Registered new user.';

            $this->session->setSessionVariable(self::$sessionUsername, $this->username);
            $this->session->setSessionVariable(self::$sessionMessage, $message);
            $this->session->regenerateId();

            $this->server->redirectToSelf();

        } catch (UserExistsException $e) {
            $this->addMessage('User exists, pick another username.');
        } catch(Exception $e) {
            $this->addMessage('There was a problem connecting to the database.');
        }

    }

    private function addMessage($message) {
        array_push($this->messages, $message);
    }

}