<?php

require_once('secrets.php');

/**
 * Database class to handle calls to the database
 * Usage: require_once("..\model\Database.php");
 * $db = new Database();
 */
class Database {

    private $db_name;
	private $db_user;
	private $db_pass;
	private $db_host;

    public function __construct() {
        $secrets = new Secrets();

        //TODO: Include host in secrets file
        $this->db_name = $secrets->db_name;
        $this->db_user = $secrets->db_user;
        $this->db_pass = $secrets->db_pass;
        $this->db_host = $secrets->db_host;

        $this->createDatabase();
        $this->createUserTable();
        $this->createCookieTable();
    }

    /**
     * Creates the database if not exists.
     * Only call from constructor
     */
    private function createDatabase() {

        //TODO: Use connect method
        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass);
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $sql = "CREATE DATABASE IF NOT EXISTS workingholidayg";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }

        //TODO: Unreachable
        $mysqli->close();

    }

    /**
     * Creates connection to MySQL server.
     */
    public function connect() {

        //TODO: Rewrite method to be able to use from constructor
        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            $this->connected = false;
            exit();
        }

        //TODO: Do I need this?
        $this->connected = true;

        return $mysqli;

    }

    /**
     * Disconnects if connection exists
     */
    public function disconnect($mysqli) {

        //TODO: Do I need this?
        if ($this->connected != false) {
            $mysqli->close();
            $this->connected = false;
        }

    }

    /**
     * Create user table in database if not exists
     * Only call from constructor
     * @return string - only if error creating table
     * TODO: Fix return
     */
    private function createUserTable() {

        $mysqli = $this->connect();

        $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        password VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP
        )";

        //TODO: Rewrite to remove empty if statement
        //TODO: Break out to query method
        if ($mysqli->query($sql) === TRUE) {

        } else {
            return "Error creating table: " . $mysqli->error;
        }

        $this->disconnect($mysqli);

    }

    /**
     * Create cookie table in database if not exists
     * Only call from constructor
     * @return string - only if error creating table return
     * TODO: Fix return
     */
    private function createCookieTable() {

        $mysqli = $this->connect();

        $sql = "CREATE TABLE IF NOT EXISTS cookies (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cookiename VARCHAR(30) NOT NULL,
        password VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP
        )";

        //TODO: Rewrite to remove empty if statement
        //TODO: Break out to query method
        if ($mysqli->query($sql) === TRUE) {

        } else {
            return "Error creating table: " . $mysqli->error;
        }

        $this->disconnect($mysqli);

    }

    /**
     * Create user in database
     * @return string with information on the result
     * TODO: Better return
     */
    public function createUser($username, $password) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $mysqli = $this->connect();

        if ($this->findUser($username)) {
            return "User exists, pick another username.";
        }

        $sql = "INSERT INTO users (username, password)
        VALUES ('$username', '$hashedPassword')";

        //TODO: Break out to query method
        if ($mysqli->query($sql) === TRUE) {
           return "Registered new user.";
        } else {
            return $mysqli->error;
        }

        //TODO: Unreachable
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