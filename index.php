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
require_once('model/UserAgent.php');

require_once('model/UserExistsException.php');
require_once('model/EmptyTableException.php');
require_once('model/MySQLQueryException.php');

require_once('view/DateTimeView.php');
require_once('view/FormView.php');
require_once('view/LayoutView.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');

require_once('secrets.php');

//TODO: Turn off for final submission
//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$router = new RouteController();

$router->route();