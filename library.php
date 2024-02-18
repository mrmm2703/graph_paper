<?php

session_start();

if (!isset($_SESSION["User"])) {
    header("Location: index.php");
}

if (!isset($_GET["project_id"])) {
    header("Location: dashboard.php");
}

if (!isset($_GET["project_name"])) {
    header("Location: dashboard.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once "db.php";
$db_con = new DatabaseConnection();
if ($db_con->connect()) {
    $lib_items = $db_con->getLibraryItemsByProject($_GET["project_id"]);
}

?>

<html>
    <head>
        <title>Project Library</title>
        <style type="text/css" media="screen">

            table {
                border-collapse: collapse;
                border: 1px solid #000000;
            }

            table td {
                border: 1px solid #000000;
            }

            td, th {
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <h1>Project Library</h1>
        <h2><?php echo $_GET["project_name"] ?></h2>
        
        <?php
        
        if ($lib_items == 0) {
            echo "No references found";
        } else {
            echo "<table><tr><th>Type</th><th>Authors</th><th>Year</th><th>Title</th><th>Source</th><th>Added</th><th>Delete</th></tr>";
            foreach ($lib_items as $ind => $row) {
                $first_names = explode(",", $row["AuthorsFirstName"]);
                $last_names = explode(",", $row["AuthorsLastName"]);
                $authors_str = "";

                foreach ($first_names as $name_ind => $first_name) {
                    $authors_str = $authors_str . $first_name . " " . substr($last_names[$name_ind], 0, 1);
                    if ($name_ind < (count($first_names) - 1)) {
                        $authors_str = $authors_str . ", ";
                    }
                }
                echo "<tr>";
                echo "<td>" . $row["SourceType"] . "</td>";
                echo "<td>" . $authors_str . "</td>";
                echo "<td>" . $row["PublishedYear"] . "</td>";
                echo "<td>" . $row["SourceTitle"] . "</td>";
                echo "<td>" . $row["Publisher"] . "</td>";
                echo "<td>" . $row["DateCreated"] . "</td>";
                echo "<td style='text-align: center;'><a href='delete_reference.php?project_name=" . $_GET["project_name"] . " &project_id=" . $_GET["project_id"] . "&library_item_id=" . $row["LibraryItemID"] . "'>X</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        ?>
        <!-- <a href="new_project.php">New project</a> -->
        <br><br>
        <ul>
            <li><a href="new_book.php?project_name=<?php echo $_GET["project_name"] ?>&project_id=<?php echo $_GET["project_id"] ?>">New book reference</a></li>
            <li><a href="new_website.php?project_name=<?php echo $_GET["project_name"] ?>&project_id=<?php echo $_GET["project_id"] ?>">New website reference</a></li>
            <li><a href="new_journal.php?project_name=<?php echo $_GET["project_name"] ?>&project_id=<?php echo $_GET["project_id"] ?>">New journal reference</a></li>        </ul>
        <a href="dashboard.php">Back to dashboard</a>
    </body>
</html>