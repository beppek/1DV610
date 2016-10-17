<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');
require_once('model/Database.php');
require_once('model/Session.php');

//TODO: Turn off for final submission
//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//CREATE OBJECTS OF THE VIEWS
$v = new LoginView();
$dtv = new DateTimeView();
$lv = new LayoutView();
$rv = new RegisterView();

$session = new Session();
if ($session->isHijacked()) {
    $lv->render($v, $dtv);
    exit;
}

//TODO: Break out to helper class
if (isset($_GET['register'])) {
    $lv->render($rv, $dtv);
} else if ($session->isLoggedIn()) {
    $lv->render($v, $dtv);
} else if (isset($_COOKIE['LoginView::CookieName'])) {
    $db = new Database();
    $name = $_COOKIE['LoginView::CookieName'];
    $password = $_COOKIE['LoginView::CookiePassword'];
    if ($db->verifyCookie($name, $password)) {
        $_SESSION['message'] = 'Welcome back with cookie';
        $_SESSION['loggedin'] = true;
        $lv->render($v, $dtv);
    } else {
        setcookie('LoginView::CookieName', '', time() - 3600);
        setcookie('LoginView::CookiePassword', '', time() - 3600);
        $_SESSION['message'] = 'Wrong information in cookies';
        $lv->render($v, $dtv);
    }
} else {
    $lv->render($v, $dtv);
}
