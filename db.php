<?php

/**
 * Connect to and query a database.
 * 
 * This class is used to create and run queries on a database. All database communication
 * should be done through this class.
 */
class DatabaseConnection {
    /**
     * The database name.
     * @var string $db_name
     */
    protected $db_name;
    /**
     * The username to use to login to the databaes.
     * @var string $db_username
     */
    protected $db_username;
    /**
     * The password to use when connecting to the database.
     * @var string $db_password
     */
    protected $db_password;
    /**
     * The host name or IP address of the MySQL server.
     * @var string $db_host
     */
    protected $db_host;
    /**
     * The mysqli object used to query the database.
     * @var mysqli $mysqli;
     */
    protected $mysqli;

    /** Constructor method to setup the database config properties.
     * 
     * @return DatabaseConnection An object which can be used to query the database.
     */
    function __construct() {
        require "db_config.php";
        $this->db_name = $db_name;
        $this->db_username = $db_username;
        $this->db_password = $db_password;
        $this->db_host = $db_host;
    }

    /** Method to initiate a mysqli object and connect to the database.
     * @return boolean|string True if connection was successful or the error if unsuccessful.
     * @see mysqli::$connect_error
     */
    public function connect() {
        // Initiate a MySQL connection
        $this->mysqli = new \mysqli(
            $this->db_host,
            $this->db_username,
            $this->db_password,
            $this->db_name
        );

        // Check for an error
        if ($this->mysqli->connect_errno) {
            $_SESSION["latest_error"] = "DBCon_Connect_ConnectErrno";
            return $this->mysqli->connect_error;
        } else {
            return true;
        }
    }

    /** Run a SQL SELECT statement.
     * 
     * @param string $sql The SQL SELECT statement to run.
     * @return boolean|int|mysqli_result If query was unsuccessful, returns false. Otherwise,
     *                                   returns 0 if no results were found or a mysql_result
     *                                   object containing the results of the query.
     */
    public function runSqlSelect($sql) {
        if (!(isset($this->mysqli))) {
            $_SESSION["latest_error"] = "DBCon_RunSqlSelect_MysqliNotInitialised";
            return false;
        }
        $result = $this->mysqli->query($sql);
        // If SQL query failed
        if (!($result)) {
            $_SESSION["latest_error"] = "DBCon_RunSqlInsert_QueryFail";
            return false;
        } else {
            // If there were no results
            if ($result->num_rows == 0) {
                return 0;
            } else {
                return $result;
            }
        }
    }

    /**
     * Run a SQL INSERT statement.
     * 
     * @param string $sql The SQL INSERT statement to run.
     * @return boolean Whether the query was successful or not.
     */
    public function runSqlInsert($sql) {
        if (!(isset($this->mysqli))) {
            echo "mysqli not set";
            $_SESSION["latest_error"] = "DBCon_RunSqlInsert_MysqliNotInitialised";
            return false;
        }
        if ($this->mysqli->query($sql) == TRUE) {
            echo "TRUE";
            return true;
        } else {
            echo "insert failed error";
            echo "<br>Attemped following SQL statement<br>";
            echo $sql;
            echo "<br>";
            $_SESSION["latest_error"] = "DBCon_RunSqlInsert_InsertFailed";
            return false;
        }
    }

    public function getUserByEmail($email) {
        $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE EmailAddress=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }

    public function insertNewUser($email, $password_hash, $first_name, $last_name, $dob) {
        var_dump($dob);
        $stmt = $this->mysqli->prepare("INSERT INTO users (FirstName, LastName, EmailAddress, PasswordHash, DateOfBirth, Verified) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password_hash, $dob);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
}
?>