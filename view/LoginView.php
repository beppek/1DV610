<?php

class LoginView extends FormView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	private $session;

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return string - generated html
	 */
	public function response() {

		$this->session = new Session();
		$lc = new LoginController();

		$lc->handleRequest();

		$message = $lc->getMessage();

		if ($this->session->isLoggedIn()) {
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
	* @return void, BUT writes to standard output!
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
	* @return void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message) {

		$username = $this->getUsername();

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

	/**
     * @return string $username if set in post or session
     */
	private function getUsername() {
		$username;
		$post = new PostData();
		$sessionUsername = 'username';

		if ($post->postVariableisSet(self::$name)) {
			$username = $post->getPostDataVariable(self::$name);
		} else if ($this->session->exists($sessionUsername)) {
			$username = $this->session->getSessionVariable($sessionUsername);
			$this->session->unsetSessionVariable($sessionUsername);
		} else {
			$username = '';
		}

		return $username;
	}

}