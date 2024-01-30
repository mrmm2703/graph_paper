<?php

session_start();

if (!isset($_SESSION["User"])) {
    header("Location: login.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (isset($_POST["projectname"])) {
    if (strlen($_POST["projectname"]) == 0) {
        echo "Project name is empty!";
    } else {
        require_once "db.php";
        $db_con = new DatabaseConnection();
        if ($db_con->connect()) {
            $existing_projects_count = $db_con->getProjectCountByNameAndUserID($_SESSION["User"]["UserID"], $_POST["projectname"]);
            if ($existing_projects_count == 0) {
                if ($db_con->insertNewProject($_SESSION["User"]["UserID"], $_POST["projectname"])) {
                    echo "New project created";
                } else {
                    echo "Could not create project";
                }
            } else {
                echo "Project name already exists";
            }
        }
    }
}

?>

<html>
    <head>
        <title>New Project</title>
    </head>
    <body>
        <h1>New Project</h1>
        <form method="POST">
            <label for="projectname">Project Name:</label>
            <input type="text" name="projectname" placeholder="Project name"><br>
            <input type="submit" value="Submit">
        </form>
        <a href="dashboard.php">Back to dashboard</a>
    </body>
</html>