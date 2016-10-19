<?php

/**
 * Use to validate register formdata
 */
class FormValidator {

    private static $formUsername = 'RegisterView::UserName';
    private static $formPassword = 'RegisterView::Password';
    private static $formPasswordRepeat = 'RegisterView::PasswordRepeat';

    private $post;

    private $errorMessages;
    private $username;
    private $password;
    private $passwordRepeat;

    public function __construct() {
        $this->post = new PostData();

        $this->errorMessages = [];
        $this->username = $this->post->getPostDataVariable(self::$formUsername);
        $this->password = $this->post->getPostDataVariable(self::$formPassword);
        $this->passwordRepeat = $this->post->getPostDataVariable(self::$formPasswordRepeat);
    }

    public function validateFormData() {
        $this->validateUsernameLength();
        $this->validatePasswordLength();
        $this->passwordsMatch();
        $this->containsInvalidCharacters($this->username);
    }

    /**
     * Adds error message if username is too short
     * @return boolean true if username is valid
     */
    private function validateUsernameLength() {
        if (strlen($this->username) < 3) {
            $errorMessage = 'Username has too few characters, at least 3 characters.';
            $this->addErrorMessage($errorMessage);
            return false;
        }
        return true;
    }

    /**
     * Adds error message if password is too short
     * @return boolean true if password is valid
     */
    private function validatePasswordLength() {
        if (strlen($this->password) < 6) {
            $errorMessage = 'Password has too few characters, at least 6 characters.';
            $this->addErrorMessage($errorMessage);
            return false;
        }
        return true;
    }

    /**
     * @return boolean true if passwords match
     */
    private function passwordsMatch() {
        if ($this->password != $this->passwordRepeat){
            $errorMessage = "Passwords do not match.";
            $this->addErrorMessage($errorMessage);
            return false;
        }
        return true;
    }

    /**
     * Ensures no harmful characters are set in the username
     * @param string $candidate the username to check for invalid characters
     * @return returns boolean true if username contains invalid characters
     */
    private function containsInvalidCharacters($candidate) {
        if (strlen($this->sanitizedValue($candidate)) < strlen($candidate)) {
            $errorMessage = "Username contains invalid characters.";
            $this->addErrorMessage($errorMessage);
            return true;
        }
        return false;
    }

    /**
     * Sanitizes a value stripping it of harmful characters
     * @param string $valueToSanitize
     * @return string sanitized value
     */
    private function sanitizedValue($valueToSanitize) {
        return strip_tags($valueToSanitize);
    }

    private function addErrorMessage($errorMessage) {
        array_push($this->errorMessages, $errorMessage);
    }

    /**
     * Checks error messages to see if there are any
     * Call after validateFormData() method
     * @return boolean true if all form data is valid
     */
    public function formDataIsValid() {

        $isValid;
        if ($this->numOfErrorMessages() === 0){
            $isValid = true;
        } else {
            $isValid = false;
        }

        return $isValid;

    }

    private function numOfErrorMessages() {
        return count($this->errorMessages);
    }

    public function getErrorMessages() {
        return $this->errorMessages;
    }
}
