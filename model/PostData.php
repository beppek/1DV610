<?php

class PostData {

    public function getPostData() {
        return $_POST;
    }

    public function postVariableisSet($postVariable) {
        if (isset($_POST[$postVariable])) {
            return true;
        }
        return false;
    }

    public function getPostDataVariable($postVariable) {
        return $_POST[$postVariable];
    }
}
