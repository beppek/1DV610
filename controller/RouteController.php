<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');
require_once('model/Database.php');
require_once('model/Session.php');
require_once('model/Cookie.php');

class RouteController {

    private $loginView;
    private $dateTimeView;
    private $layoutView;
    private $registerView;

    private $session;

    function __construct() {

        $this->loginView = new LoginView();
        $this->dateTimeView = new DateTimeView();
        $this->layoutView = new LayoutView();
        $this->registerView = new RegisterView();

        $this->session = new Session();

    }

    function route() {

        try {
            $this->preventSessionHijacking();
        } catch (Exception $e) {
            return $this->layoutView->render(false, $this->loginView, $this->dateTimeView);
        }
        if (isset($_GET['register'])) {
            $this->layoutView->render(false, $this->registerView, $this->dateTimeView);
        } else if ($this->session->isLoggedIn()) {
            $this->layoutView->render(true, $this->loginView, $this->dateTimeView);
        } else if (isset($_COOKIE['LoginView::CookieName'])) {
            $db = new Database();
            $name = $_COOKIE['LoginView::CookieName'];
            $password = $_COOKIE['LoginView::CookiePassword'];
            if ($db->verifyCookie($name, $password)) {
                $_SESSION['message'] = 'Welcome back with cookie';
                $_SESSION['loggedin'] = true;
                $this->layoutView->render(true, $this->loginView, $this->dateTimeView);
            } else {
                setcookie('LoginView::CookieName', '', time() - 3600);
                setcookie('LoginView::CookiePassword', '', time() - 3600);
                $_SESSION['message'] = 'Wrong information in cookies';
                $this->layoutView->render(false, $this->loginView, $this->dateTimeView);
            }
        } else {
            $this->layoutView->render(false, $this->loginView, $this->dateTimeView);
        }

    }

    function preventSessionHijacking() {
        if ($this->session->isHijacked()) {
            throw new Exception("Session is hijacked");
        }
    }
}
