<?php

class LoginController {

    public function checkInput($formData) {

        if (empty($formData["LoginView::UserName"])) {
            $message = 'Username is missing';
        } else if (empty($formData["LoginView::Password"])) {
            $message = 'Password is missing';
        } else {
            $message = "Wrong name or password";
        }

        return $message;

    }

}