<?php

require_once('controller/LoginController.php');
require_once('view/FormView.php');

class LoginView extends FormView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$lc = new LoginController();
			if (isset($_POST["LoginView::Logout"])) {
				$message = $lc->logout();
			} else if (!isset($_SESSION["loggedin"])) {
				$message = $lc->login($_POST);
			} else {
				$message = "";
			}
		} else if (isset($_SESSION["message"])) {
			$message = $_SESSION["message"];
			unset($_SESSION["message"]);
		} else {
			$message = '';
		}

		if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
			$response = $this->generateLogoutButtonHTML($message);
		} else {
			$response = $this->generateLoginFormHTML($message);
		}
		return $response;

	}

	/**
	* Generate HTML code on the output buffer for the logout button
	*
	* @param $message, String output message
	*
	* @return  void, BUT writes to standard output!
	*/
	private function generateLogoutButtonHTML($message) {
		return '
			<form method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	*
	* @param $message, String output message
	*
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message) {

		if (isset($_POST["LoginView::UserName"])) {
			$username = $_POST["LoginView::UserName"];
		} else if (isset($_SESSION["username"])) {
			$username = $_SESSION["username"];
			unset($_SESSION["username"]);
		} else {
			$username = "";
		}
		return '
			<form method="post">
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $username . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

}