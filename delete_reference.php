<?php

session_start();

if (!isset($_SESSION["User"])) {
    header("Location: login.php");
}

if (!isset($_GET["project_id"])) {
    header("Location: dashboard.php");
}

if (!isset($_GET["project_name"])) {
    header("Location: dashboard.php");
}

if (!isset($_GET["library_item_id"])) {
    header("Location: dashboard.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (strlen($_GET["library_item_id"]) == 0) {
    echo "Title cannot be empty!";
} else {
    require_once "db.php";
    $db_con = new DatabaseConnection();
    if ($db_con->connect()) {
        $lib_item_id = $db_con->deleteLibraryItem($_GET["library_item_id"]);
    }
}

?>

<html>
    <head>
        <title>Delete Reference</title>
    </head>
    <body>
        <h1>Deleting reference...</h1>
        <?php
            if ($lib_item_id == true) {
                echo "Deleted reference successfully";
            } else {
                echo "Failed to delete reference";
            }
        ?>
        <br><br>
        <a href="library.php?project_name=<?php echo $_GET["project_name"] ?>&project_id=<?php echo $_GET["project_id"] ?>">Back to <?php echo $_GET["project_name"] ?></a>
    </body>
</html>