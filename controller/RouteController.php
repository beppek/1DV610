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

    private static $cookieUsername = 'LoginView::CookieName';
    private static $cookiePassword = 'LoginView::CookiePassword';

    private $loginView;
    private $dateTimeView;
    private $layoutView;
    private $registerView;

    private $session;
    private $cookie;

    public function __construct() {

        $this->loginView = new LoginView();
        $this->dateTimeView = new DateTimeView();
        $this->layoutView = new LayoutView();
        $this->registerView = new RegisterView();

        $this->session = new Session();
        $this->cookie = new Cookie();

    }

    public function route() {

        if ($this->session->isLoggedIn()) {
            $this->checkSession();
        } else if ($this->urlParamIsRegister()) {
            $this->gotoRegisterPage();
        } else if ($this->cookie->exists(self::$cookieUsername)) {
            $this->loginWithCookie();
        } else {
            $isLoggedIn = false;
            $this->gotoLoginPage($isLoggedIn);
        }


    }

    private function checkSession() {
        $isLoggedIn = true;
        if ($this->session->isHijacked()) {
            $isLoggedIn = false;
        }
        $this->gotoLoginPage($isLoggedIn);
    }

    public function gotoRegisterPage() {
        $isLoggedIn = false;
        $this->layoutView->render($isLoggedIn, $this->registerView, $this->dateTimeView);
    }

    public function gotoLoginPage($isLoggedIn) {
        $this->layoutView->render($isLoggedIn, $this->loginView, $this->dateTimeView);
    }

    public function urlParamIsRegister() {
        if (isset($_GET['register'])) {
            return true;
        }
        return false;
    }

    public function loginWithCookie() {

        $db = new Database();
        $name = $this->cookie->getValue(self::$cookieUsername);
        $password = $this->cookie->getValue(self::$cookiePassword);

        $sessionMessage = 'message';
        $sessionLoggedIn = 'loggedin';

        $isLoggedIn;

        if ($db->verifyCookie($name, $password)) {

            $successMessage = 'Welcome back with cookie';

            $this->session->setSessionVariable($sessionMessage, $successMessage);
            $this->session->setSessionVariable($sessionLoggedIn, true);
            $isLoggedIn = true;

        } else {

            $wrongCookieInfoMessage = 'Wrong information in cookies';

            $this->cookie->unset(self::$cookieUsername);
            $this->cookie->unset(self::$cookiePassword);
            $this->session->setSessionVariable($sessionMessage, $wrongCookieInfoMessage);
            $isLoggedIn = false;

        }

        $this->gotoLoginPage($isLoggedIn);

    }
}
