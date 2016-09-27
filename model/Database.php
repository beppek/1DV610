<?php

require_once("secrets.php");

/**
 * Database class to handle calls to the database
 * Usage: require_once("model\Database.php");
 * $db = new Database();
 */
class Database {

    private $db_name;
	private $db_user;
	private $db_pass;
	private $db_host = "localhost";
    // private $connected = false;
    // private $mysqli;

    public function __construct() {
        $secrets = new Secrets();

        $this->db_name = $secrets->db_name;
        $this->db_user = $secrets->db_user;
        $this->db_pass = $secrets->db_pass;

        $this->createDatabase();
        $this->createUserTable();
    }

    /**
     * Creates the database if not exists.
     * Can only be created from constructor
     */
    private function createDatabase() {
        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass);
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $sql = "CREATE DATABASE IF NOT EXISTS workingholidayg";
        if ($mysqli->query($sql) === TRUE) {
            $this->connected = true;
            return true;
        } else {
            $this->connected = false;
            return false;
        }

        $mysqli->close();
        $this->connected = false;

    }

    /**
     * Creates connection to MySQL server.
     * @return true if successful connection
     */
    public function connect() {

        if ($this->connected == false) {
            $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

            if ($mysqli->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                $this->connected = false;
                exit();
            } else {
                return true;
            }

        }

    }

    /**
     * Disconnects if connection exists
     */
    public function disconnect() {

        if ($this->connected != false) {
            $this->mysqli->close();
            $this->connected = false;
        }

    }

    /**
     * Create user table in database if not exists
     * @return if error creating table return
     */
    private function createUserTable() {

        if ($this->connected == false) {
            // $this->connect();
        }

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            $this->connected = false;
            exit();
        }

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

        $mysqli->close();
        $this->connected = false;

        // $this->disconnect();

    }

    /**
     * Create user in database
     */
    public function createUser($username, $password) {

        if ($this->connected == false) {
            // $this->connect();
        }

        if ($this->findUser($username)) {
            return "User exists, pick another username.";
        }

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            $this->connected = false;
            exit();
        }

        $sql = "INSERT INTO users (username, password)
        VALUES ('$username', '$password')";

        if ($mysqli->query($sql) === TRUE) {
           return "User successfully created";
        } else {
            return $mysqli->error;
        }

        $mysqli->close();
        $this->connected = false;

    }

    /**
     * Find and authenticate the user
     * @return true if user is found and password is correct, else returns false
     */
    public function authenticateUser($username, $password) {
        if ($this->connected == false) {
            // $this->connect();
        }

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            $this->connected = false;
            exit();
        }

        if ($result = $mysqli->query("SELECT * FROM users")) {

            while($row = $result->fetch_array()) {

                $rows[] .= $row;

            }

            foreach($rows as $row) {

                if ($username === $row[1] && $password === $row[2]) {
                    // $_SESSION["username"] = $username;
                    // $_SESSION["password"] = $password;
                    return true;

                }else {
                    return false;
                }

            }

            $result->close();

        }

        $mysqli->close();
        $this->connected = false;
        // $this->disconnect();
    }

    /**
     * Find the user in database
     * @return true if user is found
     */
    public function findUser($username) {

        if ($this->connected == false) {
            // $this->connect();
        }

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            $this->connected = false;
            exit();
        }

        if ($result = $mysqli->query("SELECT username FROM users")) {

            if ($result->num_rows > 0) {

                while($row = $result->fetch_array()) {

                    $rows[] = $row;

                }

                foreach($rows as $row) {

                    if ($username === $row[1]) {
                        return true;

                    }else {
                        return false;
                    }

                }
            }

            $result->close();

        }

        $mysqli->close();
        $this->connected = false;
        // $this->disconnect();

    }

}