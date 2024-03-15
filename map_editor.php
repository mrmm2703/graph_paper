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

if (!isset($_GET["map_id"])) {
    header("Location: dashboard.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once "db.php";
$db_con = new DatabaseConnection();
if ($db_con->connect()) {
    $lib_items = $db_con->getLibraryItemsByProject($_GET["project_id"]);
    $map_data = $db_con->getMapData($_GET["map_id"])["MapData"];
    echo "<script>var db_map_data = `" . $map_data . "`</script>";
}

?>

<html>
    <head>
        <title>Test</title>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
    </head>
    <body>
        <h1>This is a test</h1>
        <select id="reference_picker"></select>
        <button id="insert-btn">Insert</button>
        <button id="save-btn">Save</button>
        <br><br>
        <button id="link-btn">Create link</button>
        <br><br>
        <a href="dashboard.php"><button id="link-btn">Back</button></a>

        </select>
        
        <div style="width: 100%; height: 100%;" id='container'></div>
    </body>
</html>

<script>
<?php
        
$js_list = "var lib_items = {";
foreach ($lib_items as $ind => $row) {
    $first_names = explode(",", $row["AuthorsFirstName"]);
    $last_names = explode(",", $row["AuthorsLastName"]);
    $authors_str = "";
    $authors_json = "[";

    foreach ($first_names as $name_ind => $first_name) {
        $authors_str = $authors_str . $first_name . " " . substr($last_names[$name_ind], 0, 1);
        if ($name_ind < (count($first_names) - 1)) {
            $authors_str = $authors_str . ", ";
        }
        $cur_json = "{first_name: '" . $first_name . "', middle_name: '" . "" . "', last_name: '" . $last_names[$name_ind] . "'},";
        $authors_json = $authors_json . $cur_json;
    }

    $authors_json = $authors_json . "]";

    $cur_obj = $row["LibraryItemID"] . ": {library_item_id: " . $row["LibraryItemID"] . ", "
        . "source_type: '" . $row["SourceType"] . "', "
        . "published_year: " . $row["PublishedYear"] . ", "
        . "source_title: '" . $row["SourceTitle"] . "', "
        . "publisher: '" . $row["Publisher"] . "', "
        . "date_created: '" . $row["DateCreated"] . "', "
        . "authors_short_str: '" . $authors_str . "', "
        . "authors: " . $authors_json . "},";
    
    $js_list = $js_list . $cur_obj;
}
$js_list = $js_list . "}";

echo $js_list;


?>
</script>

<script src="webpack/dist/main.js"></script>

<script>
              const searchParams = new URLSearchParams(window.location.search)
              if (searchParams.has("msg")) {
                alert(searchParams.get("msg"))
              }
        </script>