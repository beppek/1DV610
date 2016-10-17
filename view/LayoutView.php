<?php

require_once('model/Session.php');

class LayoutView {

  /**
   * Renders the view based on user interaction
   *
   * @param FormView $v - Abstract class. Use inheritance for call
   */
  public function render(FormView $v, DateTimeView $dtv) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn() . '

          <div class="container">
              ' . $v->response() . '

              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }

  private function renderIsLoggedIn() {
    $session = new Session();
    if ($session->isLoggedIn()) {
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
