<?php

class LoginController {

    public function checkInput($formData) {

        $username = $formData["LoginView::UserName"];
        $password = $formData["LoginView::Password"];

        if (empty($username)) {
            $message = 'Username is missing';
        } else if (empty($password)) {
            $message = 'Password is missing';
        } else {
            $message = "Wrong name or password";
        }

        return $message;

    }

}