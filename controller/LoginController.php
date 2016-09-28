<?php

require_once("model/Database.php");

class LoginController {

    private $db;

    /**
     * Checks input.
     *
     * @return Returns string if encounters errors otherwise chains the call to login.
     */
    public function login($formData) {

        $username = $formData["LoginView::UserName"];
        $password = $formData["LoginView::Password"];

        if (empty($username)) {
            return 'Username is missing';
        } else if (empty($password)) {
            return 'Password is missing';
        } else {
            return $this->authenticate($formData);
        }

        return $message;

    }

    /**
     * Function to log user in.
     *
     * @return only returns string if login fails. Otherwise redirect
     */
    public function authenticate($user) {
        $username = $user["LoginView::UserName"];
        $password = md5($user['LoginView::Password']);

        if (isset($user["LoginView::KeepMeLoggedIn"]) && $user["LoginView::KeepMeLoggedIn"] === "on") {
            $keep = true;
        } else {
            $keep = false;
        }

        $this->db = new Database();

        if ($this->db->authenticateUser($username, $password)) {
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $password;

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

            $_SESSION["loggedin"] = true;
            session_regenerate_id();
            return header("Location: " . $_SERVER['PHP_SELF']);
       } else {
            return "Wrong name or password";
       }

    }

    /**
     * Logout if user is logged in
     *
     * @return return empty string if already logged out. Otherwise redirect
     */
    public function logout() {
        if (isset($_COOKIE["username"])) {
            setcookie("LoginView::CookieName", "", time() - 3600);
            setcookie("LoginView::CookiePassword", "", time() - 3600);
        }
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            unset($_SESSION["username"]);
            unset($_SESSION["password"]);
            unset($_SESSION["loggedin"]);
            $_SESSION["message"] = "Bye bye!";
            session_regenerate_id();
            return header("Location: " . $_SERVER['PHP_SELF']);
        } else {
            $message = "";
        }
        return $message;
    }

}