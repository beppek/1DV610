<?php

require_once('model/PostData.php');

class FormValidator {

    private static $formUsername = 'RegisterView::UserName';
    private static $formPassword = 'RegisterView::Password';
    private static $formPasswordRepeat = 'RegisterView::PasswordRepeat';

    private $post;

    private $errorMessages;
    private $username;
    private $password;
    private $passwordRepeat;
    private $isValid;

    public function __construct() {
        $this->post = new PostData();

        $this->errorMessages = [];
        $this->username = $this->post->getPostDataVariable(self::$formUsername);
        $this->password = $this->post->getPostDataVariable(self::$formPassword);
        $this->passwordRepeat = $this->post->getPostDataVariable(self::$formPasswordRepeat);
    }

    public function formDataIsValid() {

        $isValid;
        if ($this->numOfErrorMessages() === 0){
            $isValid = true;
        } else {
            $isValid = false;
        }

        return $isValid;

    }

    public function validateFormData() {
        $this->validateUsernameLength();
        $this->validatePasswordLength();
        $this->passwordsMatch();
        $this->containsInvalidCharacters($this->username);
    }

    private function numOfErrorMessages() {
        return count($this->errorMessages);
    }

    private function validateUsernameLength() {
        if (strlen($this->username) < 3) {
            $errorMessage = 'Username has too few characters, at least 3 characters.';
            $this->addErrorMessage($errorMessage);
            return false;
        }
        return true;
    }

    private function validatePasswordLength() {
        if (strlen($this->password) < 6) {
            $errorMessage = 'Password has too few characters, at least 6 characters.';
            $this->addErrorMessage($errorMessage);
            return false;
        }
        return true;
    }

    private function passwordsMatch() {
        if ($this->password != $this->passwordRepeat){
            $errorMessage = "Passwords do not match.";
            $this->addErrorMessage($errorMessage);
            return false;
        }
        return true;
    }

    private function containsInvalidCharacters($candidate) {
        if (strlen($this->sanitizedValue($candidate)) < strlen($candidate)) {
            $errorMessage = "Username contains invalid characters.";
            $this->addErrorMessage($errorMessage);
            return true;
        }
        return false;
    }

    private function sanitizedValue($valueToSanitize) {
        return strip_tags($valueToSanitize);
    }

    private function addErrorMessage($errorMessage) {
        array_push($this->errorMessages, $errorMessage);
    }

    public function getErrorMessages() {
        return $this->errorMessages;
    }
}
