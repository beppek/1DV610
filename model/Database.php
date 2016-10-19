<?php

/**
* Database class to handle calls to the database
* Usage: require_once("model/Database.php");
* $db = new Database();
*/
class Database {

    private static $usersTable = 'users';
    private static $cookiesTable = 'cookies';

    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_host;


    /**
     * Constructor creates Database and tables if not exists.
     * @throws Exception if encounters errors creating database or tables
     */
    public function __construct() {

        $secrets = new Secrets();

        $this->db_name = $secrets->db_name;
        $this->db_user = $secrets->db_user;
        $this->db_pass = $secrets->db_pass;
        $this->db_host = $secrets->db_host;

        try {
            $this->createDatabase();
            $this->createTable(self::$usersTable);
            $this->createTable(self::$cookiesTable);
        } catch (Exception $e) {
            throw $e;
        }

    }

    /**
     * Creates the database if not exists.
     * Only call from constructor
     */
    private function createDatabase() {

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass);
        if ($mysqli->connect_error) {
            throw new ConnectionException('Connection failed: ' . $mysqli->connect_error);
        }

        $sql = 'CREATE DATABASE IF NOT EXISTS' . $this->db_name;
        $mysqli->query($sql);
        $mysqli->close();

    }

    /**
     * Creates connection to MySQL server.
     * Do not call from createDatabase method as database may not exist.
     */
    private function connect() {

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
        if ($mysqli->connect_errno) {
            throw new ConnectionException('Connection failed: ' . $mysqli->connect_error);
        }

        return $mysqli;

    }

    /**
     * Disconnects from database. Call after database operations are complete.
     */
    private function disconnect($mysqli) {
        $mysqli->close();
    }

    /**
     * Create table in database if not exists
     * Only call from constructor
     */
    private function createTable($tableName) {

        $mysqli = $this->connect();

        $sql = 'CREATE TABLE IF NOT EXISTS ' . $tableName . ' (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        password VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP
        )';

        if ($mysqli->query($sql) === false) {
            throw new Exception("Error creating table: " . $mysqli->error);
        }

        $this->disconnect($mysqli);

    }

    /**
     * @throws UserExistsException
     * @throws Exception - if error writing to database
     * @return void
     */
    public function createUser($username, $password) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $mysqli = $this->connect();

        if ($this->userExists($username)) {
            throw new UserExistsException();
        }

        $sql = "INSERT INTO users (username, password)
        VALUES ('$username', '$hashedPassword')";

        if ($mysqli->query($sql) === false) {
            throw new Exception();
        }

        $this->disconnect($mysqli);

    }

    /**
     * @throws MySQLQueryException if mysqli query encounters error
     * @return boolean if user could be authenticated
     */
    public function authenticateUser($username, $password) {

        $mysqli = $this->connect();

        $sql = "SELECT * FROM " . self::$usersTable;
        $result = $mysqli->query($sql);

        if ($result === false) {
            throw new MySQLQueryException();
        }

        if ($result->num_rows > 0) {

            $users = [];
            while ($tableRow = $result->fetch_array()) {
                $users[] = $tableRow;
            }

            foreach ($users as $user) {
                $userIsVerified = $this->verifyUser($user, $username, $password);
                if ($userIsVerified) {
                    $result->close();
                    $this->disconnect($mysqli);
                    return true;
                }
            }
        }

        $result->close();
        $this->disconnect($mysqli);
        return false;

    }

    private function verifyUser($storedUser, $candidateUsername, $candidatePassword) {
        $storedUsername = $storedUser[1];
        $storedPassword = $storedUser[2];

        $usernameIsVerified = $candidateUsername === $storedUsername;
        $passWordIsVerified = password_verify($candidatePassword, $storedPassword);
        if ($usernameIsVerified && $passWordIsVerified) {
            return true;
        }
        return false;
    }

    /**
     * Find the user in database
     * @throws MySQLQueryException if mysqli query encounters error
     * @return true if user is found
     */
    public function userExists($username) {

        $mysqli = $this->connect();

        $sql = "SELECT * FROM " . self::$usersTable;
        $result = $mysqli->query($sql);

        if ($result === false) {
            throw new MySQLQueryException();
        }

        if ($result->num_rows > 0) {

            $users = [];
            while ($tableRow = $result->fetch_array()) {
                $users[] = $tableRow;
            }

            foreach ($users as $user) {
                if ($username === $user[1]) {
                    $result->close();
                    $this->disconnect($mysqli);
                    return true;
                }
            }
        }

        $result->close();
        $this->disconnect($mysqli);
        return false;

    }

    /**
     * Stores the cookie with password in database to keep login for later visit
     * @param $name string username to store in database
     * @param $password string
     * @throws MySQLQueryException if mysqli encounters query error
     */
    public function storeCookie($name, $password) {

        $mysqli = $this->connect();

        $sql = "INSERT INTO cookies (username, password)
        VALUES ('$name', '$password')";

        if ($mysqli->query($sql) === false) {
            throw new MySQLQueryException($mysqli->error);
        }

        $this->disconnect($mysqli);

    }

    /**
     * Find and verify the cookie
     * @throws MySQLQueryException if mysqli query encounters an error
     * @throws WrongCookieInfoException if cookie did not match stored info
     * @return void when cookie is found and matched
     */
    public function verifyCookie($name, $password) {

        $mysqli = $this->connect();

        $sql = "SELECT * FROM " . self::$cookiesTable;
        $result = $mysqli->query($sql);

        if ($result === false) {
            throw new MySQLQueryException();
        }

        if ($result->num_rows > 0) {

            $cookies = [];
            while ($tableRow = $result->fetch_array()) {
                $cookies[] = $tableRow;
            }

            foreach ($cookies as $cookie) {
                if ($name === $cookie[1] && $password === $cookie[2]) {
                    $result->close();
                    $this->disconnect($mysqli);
                    return;
                }
            }
        }

        throw new WrongCookieInfoException();
    }

}