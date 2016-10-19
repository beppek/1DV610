<?php

class LayoutView {

    /**
    * Renders the view based on user interaction
    *
    * @param FormView $v - Abstract class. Use inheritance for call
    * @param $isLoggedIn boolean
    */
    public function render($isLoggedIn, FormView $v, DateTimeView $dtv) {
        echo '<!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <title>Login Example</title>
                </head>
                <body>
                    <h1>Assignment 2</h1>
                    ' . $this->renderIsLoggedIn($isLoggedIn) . '
                    
                    <div class="container">
                        ' . $v->response() . '
                        
                        ' . $dtv->show() . '
                    </div>
                 </body>
            </html>
        ';
    }

    /**
    * @param $isLoggedIn boolean
    * @return string HTML heading
    */
    private function renderIsLoggedIn($isLoggedIn) {
        if ($isLoggedIn) {
            return '<h2>Logged in</h2>';
        }
        else {
            return '
                ' . $this->renderLink() . '
        
                <h2>Not logged in</h2>
                ';
        }
    }

    private function renderLink() {

        if (isset($_GET["register"])) {
            $link = '<a href="?">Back to login</a>';
        } else {
            $link = '<a href="?register">Register a new user</a>';
        }

        return $link;
    }

}
