<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');
require_once('model/Database.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ini_set( 'session.use_trans_sid', false );
ini_set( 'session.cookie_httponly', true );
ini_set( 'session.use_only_cookies', true );

//CREATE OBJECTS OF THE VIEWS
$v = new LoginView();
$dtv = new DateTimeView();
$lv = new LayoutView();
$rv = new RegisterView();

session_start();

if (isset($_SESSION['HTTP_USER_AGENT'])) {
     if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
         $lv->render(false, $v, $dtv);
         exit;
     }
} else {
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
}

if (isset($_GET['register'])) {
    $lv->render(false, $rv, $dtv);
} else if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ) {
    $lv->render(true, $v, $dtv);
} else if (isset($_COOKIE['LoginView::CookieName'])) {
    $db = new Database();
    $name = $_COOKIE['LoginView::CookieName'];
    $password = $_COOKIE['LoginView::CookiePassword'];
    if ($db->verifyCookie($name, $password)) {
        $_SESSION['message'] = 'Welcome back with cookie';
        $_SESSION['loggedin'] = true;
        $lv->render(true, $v, $dtv);
    } else {
        setcookie('LoginView::CookieName', '', time() - 3600);
        setcookie('LoginView::CookiePassword', '', time() - 3600);
        $_SESSION['message'] = 'Wrong information in cookies';
        $lv->render(false, $v, $dtv);
    }
} else {
    $lv->render(false, $v, $dtv);
}
