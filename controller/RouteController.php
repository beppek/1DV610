<?php

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

    /**
     * Only access point of class
     * Routes requests through the application
     * Call from index.php
     */
    public function route() {

        $server = new ServerController();

        if ($this->session->isLoggedIn()) {
            $this->loginWithSession();
        } else if ($server->urlParamIsRegister()) {
            $this->gotoRegisterPage();
        } else if ($this->cookie->exists(self::$cookieUsername)) {
            $this->loginWithCookie();
        } else {
            $isLoggedIn = false;
            $this->gotoLoginPage($isLoggedIn);
        }

    }

    private function loginWithSession() {
        $isLoggedIn;
        if ($this->session->isHijacked()) {
            $isLoggedIn = false;
        } else {
            $isLoggedIn = true;
        }
        $this->gotoLoginPage($isLoggedIn);
    }

    private function gotoLoginPage($isLoggedIn) {
        $this->layoutView->render($isLoggedIn, $this->loginView, $this->dateTimeView);
    }

    private function gotoRegisterPage() {
        $isLoggedIn = false;
        $this->layoutView->render($isLoggedIn, $this->registerView, $this->dateTimeView);
    }

    private function loginWithCookie() {

        $db = new Database();
        $name = $this->cookie->getValue(self::$cookieUsername);
        $password = $this->cookie->getValue(self::$cookiePassword);

        $sessionMessage = 'message';
        $sessionLoggedIn = 'loggedin';

        $isLoggedIn;

        try {
            $db->verifyCookie($name, $password);
            $successMessage = 'Welcome back with cookie';
            $this->session->setSessionVariable($sessionMessage, $successMessage);
            $this->session->setSessionVariable($sessionLoggedIn, true);
            $isLoggedIn = true;
        } catch (WrongCookieInfoException $e) {

            $wrongCookieInfoMessage = 'Wrong information in cookies';

            $this->cookie->delete(self::$cookieUsername);
            $this->cookie->delete(self::$cookiePassword);
            $this->session->setSessionVariable($sessionMessage, $wrongCookieInfoMessage);
            $isLoggedIn = false;

        } catch (Exception $e) {
            $errorMessage = 'Something went wrong, try again later';
            $this->session->setSessionVariable($sessionMessage, $errorMessage);
        }

        $this->gotoLoginPage($isLoggedIn);

    }

}