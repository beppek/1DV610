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
        if ($this->postVariableisSet($postVariable)) {
            return $_POST[$postVariable];
        }
    }

    /**
     * @return string sanitized to exclude illegal characters
     */
    public function getSanitizedPostVariable($postVariable) {
        if ($this->postVariableisSet($postVariable)) {
            return strip_tags($_POST[$postVariable]);
        }
    }

}
