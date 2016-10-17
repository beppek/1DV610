<?php

require_once("model/Database.php");

class RegisterController {

    private $db;

    /**
     * Check user input
     * @return messages to display on the page
     */
    public function registerUser($formData) {

        $username = $formData["RegisterView::UserName"];
        $password = $formData["RegisterView::Password"];
        $passwordRepeat = $formData["RegisterView::PasswordRepeat"];

        //TODO: isEmpty, validate and sanitize helper methods
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
            return $this->saveToDB($formData);
        }
        return $messages;
    }

    /**
     * Register user.
     * @param $user should be the $_POST data
     * @return array response from Database->createUser().
     */
    public function saveToDB($user) {

        $username = $user["RegisterView::UserName"];
        $password = $user['RegisterView::Password'];

        $this->db = new Database();

        //TODO: Better variable name
        $res[] = $this->db->createUser($username, $password);

        //TODO: String dependency.
        if ($res[0] == "Registered new user.") {

            //TODO: Session controller
            $_SESSION["username"] = $username;
            $_SESSION["message"] = $res[0];
            session_regenerate_id();

            //TODO: Redirect controller
            return header("Location: " . $_SERVER['PHP_SELF']);
        } else {
            return $res;
        }

    }

}