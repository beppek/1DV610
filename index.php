<?php

//REQUIRE ALL THE FILES NEEDED
require_once('controller/FormValidator.php');
require_once('controller/LoginController.php');
require_once('controller/RegisterController.php');
require_once('controller/ServerController.php');
require_once('controller/RouteController.php');

require_once('model/Cookie.php');
require_once('model/Database.php');
require_once('model/PostData.php');
require_once('model/Session.php');

require_once('model/exceptions/UserExistsException.php');
require_once('model/exceptions/MySQLQueryException.php');
require_once('model/exceptions/WrongCookieInfoException.php');
require_once('model/exceptions/ConnectionException.php');

require_once('view/500.php');
require_once('view/DateTimeView.php');
require_once('view/FormView.php');
require_once('view/LayoutView.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');

require_once('secrets.php');

$router = new RouteController();

try {
    $router->route();
} catch (Exception $e) {
    $errorPage = new InternalServerError();
    $errorPage->render500ErrorPage();
}