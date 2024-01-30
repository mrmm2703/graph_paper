<?php

session_start();

if (isset($_SESSION["User"])) {
    header("Location: dashboard.php");
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (isset($_POST["email"])) {
    if ($_POST["password"] != $_POST["verifypassword"]) {
        echo "Passwords don't match!";
    } else if (strlen($_POST["firstname"]) < 3) {
        echo "First name is too short!";
    } else if (strlen($_POST["lastname"]) < 3) { 
        echo "Last name is too short!";
    } else if (strlen($_POST["password"]) < 8) { 
        echo "Password is too short!";
    } else {
        require_once "db.php";
        $db_con = new DatabaseConnection();
        if ($db_con->connect()) {
            $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

            $user = $db_con->getUserByEmail($_POST["email"]);
            if ($user == 0) {
                if ($db_con->insertNewUser($_POST["email"], $password_hash, $_POST["firstname"], $_POST["lastname"], $_POST["dob"]) == 1) {
                    echo "User account created";
                } else {
                    echo "Could not create user account";
                }
            } else {
                echo "Email already exists.";
            }
        }
    }
}

?>

<html>
    <head>
        <title>Register for Graph Paper</title>
    </head>
    <body>
        <h1>Register</h1>
            <label for="firstname">First Name:</label>
            <input type="text" name="firstname" placeholder="First name"><br>
            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname" placeholder="Last name"><br>
            <label for="email">Email Address:</label>
            <input type="email" name="email" placeholder="Email"><br>
            <label for="dob">Date of Birth:</label>
            <input type="date" name="dob"><br>
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Password"><br>
            <label for="verifypassword">Verify Password::</label>
            <input type="password" name="verifypassword" placeholder="Verify password"><br><br>
            <input type="submit" value="Submit">
        </form>
        <a href="index.php">Back to login</a>
    </body>
</html>