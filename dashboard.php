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
    // if ($user == 0) {
    //     echo "<br><br>User not found!";
    // } else {
    //     echo "<br><br>Found user!<br>";
    //     echo $user["FirstName"];
    //     if (password_verify($password, $user["PasswordHash"])) {
    //         echo "<br><br>Successfully logged in!";
    //         $_SESSION["User"] = $user;
    //         header("Location: dashboard.php");
    //     } else {
    //         echo "<br><br>Incorrect password!";
    //     }
    // }
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
                $all_rows = array();
                foreach ($projects_maps as $index => $row) {
                    $all_rows[$row['ProjectName']][$index] = $row;
                }
                // var_dump($all_rows);

                // for each project
                foreach ($all_rows as $project_name => $maps) {
                    echo "<h3>" . $project_name . "</h1>";
                    echo "<ul>";
                    foreach ($maps as $index => $map) {
                        echo "<li>" . $map["MapName"] . "<br>Last modified: " . $map["MapLastModified"] . "</li>";
                    }
                    echo "</ul>";
                }
            }
        ?>
        <a href="logout.php">Logout</a>
    </body>
</html>