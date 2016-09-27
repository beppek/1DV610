<?php

class RegisterController {

    public function checkInput($formData) {
        if (empty($formData["RegisterView::UserName"]) && empty($formData["RegisterView::Password"])) {
            $messages = ['Username has too few characters, at least 3 characters.', 'Password has too few characters, at least 6 characters.'];
        } else if (strlen($formData["RegisterView::UserName"]) < 3) {
            $messages = ['Username has too few characters, at least 3 characters.'];
        } else if (strlen($formData["RegisterView::Password"]) < 6) {
            $messages = ['Password has too few characters, at least 6 characters.'];
        } else {
            $messages = ["Passed check"];
        }
        return $messages;
    }

}