<?php

class Database {

    //TODO: Change these
    protected $db_name = "databasename";
	protected $db_user = "databaseuser";
	protected $db_pass = "databasepassword";
	protected $db_host = "databasehost";

    public function connect() {
        $connect_db = new mysqli( $this->db_host, $this->db_user, $this->db_pass, $this->db_name );

        if ( mysqli_connect_errno() ) {
			printf("Connection failed: %s\ ", mysqli_connect_error());
			exit();
		}
		return true;
    }

}