<?php

require_once("model/Database.php");

class LoginController {

    private $db;

    public function checkInput($formData) {

        $username = $formData["LoginView::UserName"];
        $password = $formData["LoginView::Password"];

        if (empty($username)) {
            $message = 'Username is missing';
        } else if (empty($password)) {
            $message = 'Password is missing';
        } else {
            $message = "Passed check";
        }

        return $message;

    }

    public function login($user) {
        $username = $user["LoginView::UserName"];
        $password = $user["LoginView::Password"];

        $this->db = new Database();

       if ($this->db->authenticateUser($username, $password)) {
           return "Logged in";
       } else {
           return "Wrong name or password";
       }

    }

}