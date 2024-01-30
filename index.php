<?php

session_start();

if (isset($_SESSION["UserID"])) {
    header("Location: dashboard.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (isset($_POST["email"])) {
    require_once "db.php";
    $db_con = new DatabaseConnection();
    if ($db_con->connect()) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $user = $db_con->getUserByEmail($email);
        if ($user == 0) {
            echo "<br><br>User not found!";
        } else {
            echo "<br><br>Found user!<br>";
            echo $user["FirstName"];
            if (password_verify($password, $user["PasswordHash"])) {
                echo "<br><br>Successfully logged in!";
                $_SESSION["User"] = $user;
                header("Location: dashboard.php");
            } else {
                echo "<br><br>Incorrect password!";
            }
        }
    }
}

?>
<html>
    <head>
        <title>Graph Paper</title>
    </head>
    <body>
        <h1>Graph Paper</h1>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" id="inpEmail" name="email" placeholder="Email">
            <br>
            <label for="password">Password:</label>
            <input type="password" id="inpPassword" name="password" placeholder="Password">
            <br><br>
            <input type="submit" value="Submit">
        </form>
        <a href="register.php">Register</a>
    </body>
</html>