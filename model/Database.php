<?php

/**
 * Database class to handle calls to the database
 * Usage: require_once("..\model\Database.php");
 * $db = new Database();
 */
class Database {

    private static $usersTable = 'users';
    private static $cookiesTable = 'cookies';

    private $db_name;
	private $db_user;
	private $db_pass;
	private $db_host;


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
            //TODO: Write to error log
        }

    }

    /**
     * Creates the database if not exists.
     * Only call from constructor
     */
    private function createDatabase() {

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass);
        if ($mysqli->connect_error) {
            throw new Exception('Connection failed: ' . $mysqli->connect_error);
        }

        $dbWasCreated;
        $sql = 'CREATE DATABASE IF NOT EXISTS' . $this->db_name;
        $mysqli->query($sql);
        $mysqli->close();

    }

    /**
     * Creates connection to MySQL server.
     * Do not call from constructor as database may not exist.
     */
    public function connect() {

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
        if ($mysqli->connect_errno) {
            throw new Exception('Connection failed: ' . $mysqli->connect_error);
        }

        return $mysqli;

    }

    /**
     * Disconnects from database. Call after database operations are complete.
     */
    public function disconnect($mysqli) {
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
     * Create user in database
     * @throws UserExistsException
     * @throws Exception - if error writing to database
     * @return void
     */
    public function createUser($username, $password) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $mysqli = $this->connect();

        if ($this->findUser($username)) {
            throw new UserExistsException();
        }

        $sql = "INSERT INTO users (username, password)
        VALUES ('$username', '$hashedPassword')";

        if ($mysqli->query($sql) === false) {
            throw new Exception($mysqli->error);
        }

        $this->disconnect($mysqli);

    }

    /**
     * Find and authenticate the user
     * @return true if user is found and password is correct, else returns false
     */
    public function authenticateUser($username, $password) {

        $mysqli = $this->connect();

        //TODO: Can I break out  to query method?
        if ($result = $mysqli->query("SELECT * FROM users")) {
            if ($result->num_rows > 0) {

                //TODO: Rename vars $rows[] -> $users[] and $row -> $user
                while($row = $result->fetch_array()) {
                    $rows[] = $row;
                }

                foreach($rows as $row) {
                    if ($username === $row[1] && password_verify($password, $row[2])) {
                        return true;
                    }
                }

                return false;
            }
            $result->close();
        }
        $this->disconnect($mysqli);

        return false;
    }

    /**
     * Find the user in database
     * @return true if user is found
     */
    public function findUser($username) {

        $mysqli = $this->connect();

        //TODO: Query method?
        if ($result = $mysqli->query("SELECT * FROM users")) {
            if ($result->num_rows > 0) {

                //TODO: REname vars
                while($row = $result->fetch_array()) {
                    $rows[] = $row;
                }

                foreach($rows as $row) {
                    if ($username === $row[1]) {
                        return true;
                    }
                }

                return false;
            }
            $result->close();

        }
        $this->disconnect($mysqli);

        return false;

    }

    /**
     * Stores the cookie with password in db to verify
     */
    public function storeCookie($name, $password) {

        $mysqli = $this->connect();

        $sql = "INSERT INTO cookies (cookiename, password)
        VALUES ('$name', '$password')";

        //TODO: Query method
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return $mysqli->error;
        }

        //TODO: Unreachable
        $this->disconnect($mysqli);

    }

    /**
     * Find and verify the cookie
     *
     * @return true if cookie is found and password is correct, else returns false
     */
    public function verifyCookie($name, $password) {

        $mysqli = $this->connect();

        //TODO: Query method
        if ($result = $mysqli->query("SELECT * FROM cookies")) {
            if ($result->num_rows > 0) {

                while($row = $result->fetch_array()) {
                    $rows[] = $row;
                }

                foreach($rows as $row) {
                    if ($name === $row[1] && $password === $row[2]) {
                        return true;
                    }

                }

                return false;
            }
            $result->close();

        }
        $this->disconnect($mysqli);

        return false;
    }

}