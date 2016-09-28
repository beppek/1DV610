<?php

require_once("secrets.php");

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

        $this->db_name = $secrets->db_name;
        $this->db_user = $secrets->db_user;
        $this->db_pass = $secrets->db_pass;
        $this->db_host = "localhost";

        $this->createDatabase();
        $this->createUserTable();
    }

    /**
     * Creates the database if not exists.
     * Only call from constructor
     */
    private function createDatabase() {
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

        $mysqli->close();

    }

    /**
     * Creates connection to MySQL server.
     * @return true if successful connection
     */
    public function connect() {

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            $this->connected = false;
            exit();
        }

        $this->connected = true;
        return $mysqli;


    }

    /**
     * Disconnects if connection exists
     */
    public function disconnect($mysqli) {

        if ($this->connected != false) {
            $mysqli->close();
            $this->connected = false;
        }

    }

    /**
     * Create user table in database if not exists
     * Only call from constructor
     * @return if error creating table return
     */
    private function createUserTable() {

        $mysqli = $this->connect();

        $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        password VARCHAR(30) NOT NULL,
        reg_date TIMESTAMP
        )";

        if ($mysqli->query($sql) === TRUE) {

        } else {
            return "Error creating table: " . $mysqli->error;
        }

        $this->disconnect($mysqli);

    }

    /**
     * Create user in database
     * @return text string with information on the result
     */
    public function createUser($username, $password) {

        $mysqli = $this->connect();

        if ($this->findUser($username)) {
            return "User exists, pick another username.";
        }

        $sql = "INSERT INTO users (username, password)
        VALUES ('$username', '$password')";

        if ($mysqli->query($sql) === TRUE) {
           return "Registered new user.";
        } else {
            return $mysqli->error;
        }

        $this->disconnect($mysqli);

    }

    /**
     * Find and authenticate the user
     * @return true if user is found and password is correct, else returns false
     */
    public function authenticateUser($username, $password) {
        $mysqli = $this->connect();

        if ($result = $mysqli->query("SELECT * FROM users")) {

            if ($result->num_rows > 0) {

                while($row = $result->fetch_array()) {

                    $rows[] = $row;

                }

                foreach($rows as $row) {

                    if ($username === $row[1] && $password === $row[2]) {
                        return true;

                    }else {
                        return false;
                    }

                }
            }

            $result->close();

        }

        $this->disconnect($mysqli);
    }

    /**
     * Find the user in database
     * @return true if user is found
     */
    public function findUser($username) {

        $mysqli = $this->connect();

        if ($result = $mysqli->query("SELECT * FROM users")) {

            if ($result->num_rows > 0) {

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

    }

}