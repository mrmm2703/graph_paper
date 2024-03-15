<?php

session_start();

if (!isset($_SESSION["User"])) {
    header("Location: index.php");
}

if (!isset($_POST["map_id"])) {
    header("Location: dashboard.php");
}

if (!isset($_POST["map_data"])) {
    header("Location: dashboard.php");
}

if (!isset($_POST["project_name"])) {
    header("Location: dashboard.php");
}

if (!isset($_POST["project_id"])) {
    header("Location: dashboard.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$replace_chars = array("\r", "\n", "\r\n");
$map_data = $_POST["map_data"];
$map_data = preg_replace( "/\r|\n|\r\n/", "", $map_data );

var_dump($_POST["map_data"]);
echo "<br><br>";
var_dump($map_data);
echo "<br><br>";
var_dump($_POST["map_id"]);


require_once "db.php";
$db_con = new DatabaseConnection();
if ($db_con->connect()) {
    if ($db_con->updateMapData($_POST["map_id"], $map_data)) {
        header("Location: map_editor.php?map_id=" . $_POST["map_id"] . "&project_name=" . $_POST["project_name"] . "&project_id=" . $_POST["project_id"] . "&msg=Successfully saved");
        echo "Saved successfully";
    } else {
        header("Location: map_editor.php?map_id=" . $_POST["map_id"] . "&project_name=" . $_POST["project_name"] . "&project_id=" . $_POST["project_id"] . "&msg=No changes saved");
        echo "Failed to save";
    }
}

?>