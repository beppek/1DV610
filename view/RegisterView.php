<?php

class RegisterView extends FormView {
    private static $register = 'RegisterView::Register';
    private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
    private static $messageId = 'RegisterView::Message';

    /**
     * Create HTTP response
     *
     * Call if url param is ?register
     */
     public function response() {

        $rc = new RegisterController();
        $rc->handleRequest();
        $messages = $rc->getMessages();

        $response = $this->generateRegisterFormHTML($messages);
        return $response;
     }

     private function generateRegisterFormHTML($messages) {

        $username = $this->getUsername();

		return '
            <h2>Register new user</h2>
			<form method="post" action="?register">
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
                    <p id="' . self::$messageId . '">' . $this->renderMessages($messages) . '</p>


					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $username . '" />
                    <br>
					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />
                    <br>
                    <label for="' . self::$passwordRepeat . '">Repeat password :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />
                    <br>

					<input type="submit" name="' . self::$register . '" value="Register" />
				</fieldset>
			</form>
		';
	}

    /**
     * Render messages to display inside p element
     *
     * @param $messages - expects an array
     * @return string - html output of messages
     */
    private function renderMessages($messages) {

        $htmlOutput = '';
        if (count($messages) == 0) {
            return $htmlOutput;
        } else {
            foreach($messages as $message) {
                $htmlOutput .= $message . '<br>';
            }
        }
        return $htmlOutput;
    }

    private function getUsername() {
		$username;
		$post = new PostData();
		$sessionUsername = 'username';

		if ($post->postVariableisSet(self::$name)) {
			$username = $post->getSanitizedPostVariable(self::$name);
		} else {
			$username = '';
		}

		return $username;
	}

}