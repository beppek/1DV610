<?php

require_once("model/Database.php");

class LoginController {

    private $db;

    /**
     * Checks input.
     *
     * @return string if encounters errors otherwise chains the call to authenticate.
     */
    public function login($formData) {

        //TODO: Change $formData arg to only include what is needed and not using strings
        $username = $formData["LoginView::UserName"];
        $password = $formData["LoginView::Password"];

        //TODO: isEmpty helper method
        if (empty($username)) {
            return 'Username is missing';
        } else if (empty($password)) {
            return 'Password is missing';
        } else {
            return $this->authenticate($formData);
        }

    }

    /**
     * Function to log user in.
     *
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
            return "Wrong name or password";
       }

    }

    /**
     * Logout if user is logged in
     *
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
            $message = "";
        }
        return $message;
    }

}