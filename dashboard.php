<?php

session_start();

if (!isset($_SESSION["User"])) {
    header("Location: index.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once "db.php";
$db_con = new DatabaseConnection();
if ($db_con->connect()) {

    $projects_maps = $db_con->getProjectsAndMaps($_SESSION["User"]["UserID"]);
    $projects_raw = $db_con->getProjectsByUserID($_SESSION["User"]["UserID"]);
}

?>

<html>
    <head>
        <title>Dashboard</title>
    </head>
    <body>
        <h1>Dashboard</h1>
        <h2>Welcome <?php echo $_SESSION["User"]["FirstName"] ?></h2>
        <?php
            if ($projects_maps == 0) {
                echo "No projects found";
            } else {
                $projects = array();
                foreach ($projects_raw as $index => $row) {
                    $projects[$row["ProjectID"]] = $row["ProjectName"];
                }

                $all_rows = array();
                foreach ($projects_maps as $index => $row) {
                    $all_rows[$row['ProjectID']][$index] = $row;
                }

                // for each project 
                foreach ($projects as $project_id => $project_name) {
                    echo "<h3>" . $project_name . "</h1>";
                    if (isset($all_rows[$project_id])) {
                        echo "<ul>";
                        foreach ($all_rows[$project_id] as $map_proj_id => $map) {
                            echo "<li>" . $map["MapName"] . "<br>Last modified: " . $map["MapLastModified"] . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<ul><li>No maps</li></ul>";
                    }
                }

                // // for each project
                // foreach ($all_rows as $project_name => $maps) {
                //     echo "<h3>" . $project_name . "</h1>";
                //     echo "<ul>";
                //     foreach ($maps as $index => $map) {
                //         echo "<li>" . $map["MapName"] . "<br>Last modified: " . $map["MapLastModified"] . "</li>";
                //     }
                //     echo "</ul>";
                // }
            }
        ?>

        <a href="new_project.php">New project</a>
        <a href="logout.php">Logout</a>
    </body>
</html>