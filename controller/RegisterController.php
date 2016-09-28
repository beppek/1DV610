<?php

require_once("model/Database.php");

class RegisterController {

    private $db;

    /**
     * Check user input
     * @return messages to display on the page
     */
    public function checkInput($formData) {

        $username = $formData["RegisterView::UserName"];
        $password = $formData["RegisterView::Password"];
        $passwordRepeat = $formData["RegisterView::PasswordRepeat"];

        if (empty($username) && empty($password)) {
            $messages = ['Username has too few characters, at least 3 characters.', 'Password has too few characters, at least 6 characters.'];
        } else if (strlen($username) < 3) {
            $messages = ['Username has too few characters, at least 3 characters.'];
        } else if (strlen($password) < 6) {
            $messages = ['Password has too few characters, at least 6 characters.'];
        } else if ($password != $passwordRepeat){
            $messages = ["Passwords do not match."];
        } else if (strlen(strip_tags($username)) < strlen($username)) {
            $messages = ["Username contains invalid characters."];
        } else {
            $messages = ["Passed check"];
        }
        return $messages;
    }

    /**
     * Register user.
     * @param $user should be the $_POST data
     * @return response from Database->createUser().
     */
    public function registerUser($user) {

        $username = $user["RegisterView::UserName"];
        $password = $user["RegisterView::Password"];

        $this->db = new Database();

        $res = $this->db->createUser($username, $password);

        if ($res == "Registered new user.") {
            $_SESSION["username"] = $username;
            $_SESSION["message"] = $res;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            return $res;
        }

    }

}