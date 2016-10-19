# Login_1DV610
Interface repository for 1DV610 assignment 2 and 4

## To make this application work locally follow the steps below
1. Clone the repo
2. Create settings.php containing information as described below
3. Serve up the file using php and MySQL capable server technology (eg XAMPP, MAMP etc)
4. Create a user (you must create the first user yourself, the application comes with a clean database)

### secrets.php
The file secrets.php should contain your database settings. 
To get these you need to access the admin page of your MySQL server.

The file should look as follows:
```php
<?php

class Secrets {

    public $db_pass = "yourPassWordHere";
    public $db_user = "yourUsernameHere";
    public $db_name = "yourDatabaseNameHere";
    public $db_host = "yourHostnameHere";

}
```