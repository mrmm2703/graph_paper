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
        $stmt = $this->mysqli->prepare("INSERT INTO users (FirstName, LastName, EmailAddress, PasswordHash, DateOfBirth, Verified) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password_hash, $dob);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getProjectsByUserID($user_id) {
        $stmt = $this->mysqli->prepare("SELECT ProjectName, ProjectID from projects WHERE OwnerID=?;");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return 0;
        }
    }

    public function getProjectsAndMaps($user_id) {
        $stmt = $this->mysqli->prepare("SELECT maps.MapID, maps.ProjectID, maps.MapName, maps.LastModified as MapLastModified, maps.DateCreated as MapDateCreated, 
        projects.ProjectName, projects.DateCreated as ProjectDateCreated, projects.LastModified as ProjectDateModified, projects.ShareLibrary 
        FROM maps INNER JOIN projects ON maps.ProjectID=projects.ProjectID WHERE projects.OwnerID=?;");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return 0;
        }
    }

    public function getProjectCountByNameAndUserID($user_id, $project_name) {
        $stmt = $this->mysqli->prepare("SELECT ProjectID from projects WHERE OwnerID=? AND ProjectName=?");
        $stmt->bind_param("is", $user_id, $project_name);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows;
    }

    public function insertNewProject($user_id, $project_name) {
        $stmt = $this->mysqli->prepare("INSERT INTO projects (OwnerID, ProjectName, ShareLibrary) VALUES (?, ?, 1)");
        $stmt->bind_param("is", $user_id, $project_name);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getLibraryItemsByProject($project_id) {
        $stmt = $this->mysqli->prepare("SELECT library_items.LibraryItemID, (SELECT GROUP_CONCAT(authors.AuthorFirstName) FROM authors WHERE authors.LibraryItemID=library_items.LibraryItemID) AS AuthorsFirstName, 
        (SELECT GROUP_CONCAT(authors.AuthorLastName) FROM authors WHERE authors.LibraryItemID=library_items.LibraryItemID) AS AuthorsLastName, YEAR(library_items.PublishedDate) AS PublishedYear, 
        library_items.SourceTitle, websites.Publisher, library_items.DateCreated, 'Website' AS SourceType
        FROM websites
        INNER JOIN library_items
        ON library_items.LibraryItemID=websites.LibraryItemID
        INNER JOIN authors
        ON authors.LibraryItemID=library_items.LibraryItemID
        WHERE library_items.ProjectID=?
        UNION
        SELECT library_items.LibraryItemID, (SELECT GROUP_CONCAT(authors.AuthorFirstName) FROM authors WHERE authors.LibraryItemID=library_items.LibraryItemID) AS AuthorsFirstName, 
        (SELECT GROUP_CONCAT(authors.AuthorLastName) FROM authors WHERE authors.LibraryItemID=library_items.LibraryItemID) AS AuthorsLastName, YEAR(library_items.PublishedDate) AS PublishedYear, 
        library_items.SourceTitle, books.Publisher, library_items.DateCreated, 'Book' AS SourceType
        FROM books
        INNER JOIN library_items
        ON library_items.LibraryItemID=books.LibraryItemID
        INNER JOIN authors
        ON authors.LibraryItemID=library_items.LibraryItemID
        WHERE library_items.ProjectID=?
        UNION
        SELECT library_items.LibraryItemID, (SELECT GROUP_CONCAT(authors.AuthorFirstName) FROM authors WHERE authors.LibraryItemID=library_items.LibraryItemID) AS AuthorsFirstName, 
        (SELECT GROUP_CONCAT(authors.AuthorLastName) FROM authors WHERE authors.LibraryItemID=library_items.LibraryItemID) AS AuthorsLastName, YEAR(library_items.PublishedDate) AS PublishedYear, 
        library_items.SourceTitle, '', library_items.DateCreated, 'Journal' AS SourceType
        FROM journals
        INNER JOIN library_items
        ON library_items.LibraryItemID=journals.LibraryItemID
        INNER JOIN authors
        ON authors.LibraryItemID=library_items.LibraryItemID
        WHERE library_items.ProjectID=?");
        $stmt->bind_param("iii", $project_id, $project_id, $project_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return 0;
        }
    }


    public function insertNewWebsite($project_id, $title, $summary, $published_date, $publisher, $website_title, $url, $accessed_date) {
        var_dump($accessed_date);
        if ($accessed_date == "") {
            $accessed_date = null;
        }
        $library_item_id = $this->insertNewLibraryItem($project_id, $title, $summary, $published_date);
        if ($library_item_id == false) {
            return false;
        }
        $stmt = $this->mysqli->prepare("INSERT INTO websites (LibraryItemID, WebsiteTitle, Publisher, URL, AccessedDate)
        VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $library_item_id, $website_title, $publisher, $url, $accessed_date);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            return $library_item_id;
        } else {
            return false;
        }
    }

    public function insertNewJournal($project_id, $title, $summary, $published_date, $journal_title, $doi, $start_page, $end_page) {
        $library_item_id = $this->insertNewLibraryItem($project_id, $title, $summary, $published_date);
        if ($library_item_id == false) {
            return false;
        }
        $stmt = $this->mysqli->prepare("INSERT INTO journals (LibraryItemID, JournalTitle, DOI, StartPage, EndPage)
        VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issii", $library_item_id, $journal_title, $doi, $start_page, $end_page);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            return $library_item_id;
        } else {
            return false;
        }
    }

    public function insertNewBook($project_id, $title, $summary, $published_date, $publisher, $publisher_city, $isbn) {
        $library_item_id = $this->insertNewLibraryItem($project_id, $title, $summary, $published_date);
        if ($library_item_id == false) {
            return false;
        }
        $stmt = $this->mysqli->prepare("INSERT INTO books (LibraryItemID, Publisher, PublisherCity, ISBN)
        VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $library_item_id, $publisher, $publisher_city, $isbn);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            return $library_item_id;
        } else {
            return false;
        }
    }

    private function insertNewLibraryItem($project_id, $title, $summary, $published_date) {
        $stmt = $this->mysqli->prepare("INSERT INTO library_items (ProjectID, SourceTitle, PublishedDate, Summary, DateCreated)
        VALUES (?, ?, ?, ?, CURRENT_DATE)");
        $stmt->bind_param("isss", $project_id, $title, $published_date, $summary);
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            return $this->mysqli->insert_id;
        } else {
            return false;
        }
    }

    public function insertNewAuthor($library_item_id, $first_name, $middle_name, $last_name) {
        $stmt = $this->mysqli->prepare("INSERT INTO authors (LibraryItemID, AuthorFirstName, AuthorMiddleName, AuthorLastName)
        VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $library_item_id, $first_name, $middle_name, $last_name);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
}
?>