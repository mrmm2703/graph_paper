<?php

session_start();

if (!isset($_SESSION["User"])) {
    header("Location: index.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

?>

<html>
    <head>
        <title>Dashboard</title>
    </head>
    <body>
        <h1>Dashboard</h1>
        <h2>Welcome <?php echo $_SESSION["User"]["FirstName"] ?></h2>
        <a href="logout.php">Logout</a>
    </body>
</html>