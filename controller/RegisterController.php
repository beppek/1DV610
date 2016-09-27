<?php

class RegisterController {

    public function checkInput($formData) {

        $username = $formData["RegisterView::UserName"];
        $password = $formData["RegisterView::Password"];

        if (empty($username) && empty($password)) {
            $messages = ['Username has too few characters, at least 3 characters.', 'Password has too few characters, at least 6 characters.'];
        } else if (strlen($username) < 3) {
            $messages = ['Username has too few characters, at least 3 characters.'];
        } else if (strlen($password) < 6) {
            $messages = ['Password has too few characters, at least 6 characters.'];
        } else {
            $messages = ["Passed check"];
        }
        return $messages;
    }

}