<?php

require_once('controller/RegisterController.php');
require_once('view/FormView.php');

class RegisterView extends FormView {
    private static $register = 'RegisterView::Register';
    private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
	private static $cookieName = 'RegisterView::CookieName';
	private static $cookiePassword = 'RegisterView::CookiePassword';
    private static $messageId = 'RegisterView::Message';

    /**
     * Create HTTP response
     *
     * Call if url param is ?register
     */
     public function response() {

         //TODO: Break out to helper method/class
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $rc = new RegisterController();
            $messages = $rc->registerUser($_POST);
		} else {
			$messages = [];
		}
         $response = $this->generateRegisterFormHTML($messages);
         return $response;
     }

     private function generateRegisterFormHTML($messages) {

         //TODO: Break out to helper method
         if (isset($_POST['RegisterView::UserName'])) {
			$username = strip_tags($_POST['RegisterView::UserName']);
		} else {
			$username = '';
		}

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
     */
    private function renderMessages($messages) {

        //TODO: rename var
        $str = '';
        if (count($messages) == 0) {
            return $str;
        } else {
            foreach($messages as $message) {
                $str .= $message . '<br>';
            }
        }
        return $str;
    }


}