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
     * @param string $username
     * @param string $password - must be unhashed to use password_verify
     * @throws MySQLQueryException if mysqli query encounters error
     * @return boolean if user could be authenticated
     */
    public function authenticateUser($username, $password) {

        $mysqli = $this->connect();

        $query = "SELECT password FROM " . self::$usersTable . " WHERE username=?";
        $preparedQuery = $mysqli->prepare($query);

        if ($preparedQuery == false) {
            throw new MySQLQueryException();
        }

        $preparedQuery->bind_param("s", $username);
        $preparedQuery->execute();
        $preparedQuery->bind_result($storedPassword);
        $preparedQuery->fetch();

        $passwordIsVerified = password_verify($password, $storedPassword);

        $preparedQuery->close();
        $this->disconnect($mysqli);

        if ($passwordIsVerified) {
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

        $query = "SELECT username FROM " . self::$usersTable . " WHERE username=?";
        $preparedQuery = $mysqli->prepare($query);

        if ($preparedQuery == false) {
            throw new MySQLQueryException();
        }

        $preparedQuery->bind_param("s", $username);
        $preparedQuery->execute();
        $preparedQuery->bind_result($storedUsername);
        $preparedQuery->fetch();

        if ($storedUsername == $username) {
            return true;
        }

        return false;

    }

    /**
     * Stores the cookie with password in database to keep login for later visit
     * @param $username string
     * @param $password string a randomly generated hash that is stored in the cookie
     * @throws MySQLQueryException if mysqli encounters query error
     */
    public function storeCookie($username, $password) {

        $mysqli = $this->connect();

        $sql = "INSERT INTO cookies (username, password)
        VALUES ('$username', '$password')";

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
    public function verifyCookie($username, $password) {

        $mysqli = $this->connect();

        $query = "SELECT password FROM " . self::$cookiesTable . " WHERE username=?";
        $preparedQuery = $mysqli->prepare($query);

        if ($preparedQuery == false) {
            throw new MySQLQueryException();
        }

        $storedPasswords = [];
        $preparedQuery->bind_param("s", $username);
        $preparedQuery->execute();
        $preparedQuery->bind_result($retrievedPassword);
        while ($preparedQuery->fetch()) {
            $storedPasswords[] = $retrievedPassword;
        }

        $preparedQuery->close();
        $this->disconnect($mysqli);

        foreach ($storedPasswords as $storedPassword) {
            if ($storedPassword === $password) {
                return;
            }
        }

        throw new WrongCookieInfoException();
    }

}