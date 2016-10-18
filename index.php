<?php

require_once('controller/RouteController.php');

//TODO: Turn off for final submission
//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$router = new RouteController();

$router->route();