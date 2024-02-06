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

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (isset($_POST["title"])) {
    if (strlen($_POST["title"]) == 0) {
        echo "Title cannot be empty!";
    } else if (isset($_POST["authorfirstname"]) && strlen($_POST["authorfirstname"]) == 0) {
        echo "Author first name cannot be empty!";
    } else if (isset($_POST["authorlastname"]) && strlen($_POST["authorlastname"]) == 0) {
        echo "Author last name cannot be empty!";
    } else {
        require_once "db.php";
        $db_con = new DatabaseConnection();
        if ($db_con->connect()) {
            $lib_item_id = $db_con->insertNewBook($_GET["project_id"], $_POST["title"], $_POST["summary"], $_POST["publisheddate"], $_POST["publisher"], $_POST["publishercity"], $_POST["isbn"]);
            $db_con->insertNewAuthor($lib_item_id, $_POST["authorfirstname"], $_POST["authormiddlename"], $_POST["authorlastname"]);
        }
    }
}

?>

<html>
    <head>
        <title>New Book</title>
    </head>
    <body>
        <h1>New Book</h1>
        <form method="POST">
            <!-- <label for="firstname">First Name:</label>
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
            <input type="password" name="verifypassword" placeholder="Verify password"><br><br> -->

            <label for="title">Title:</tabel>
            <input type="text" name="title" placeholder="Title"><br>
            <label for="publisheddate">Published date:</label>
            <input type="date" name="publisheddate"><br>
            <label for="summary">Summary:</label>
            <input type="textarea" name="summary" rows="5" cols="50" placeholder="Summary"><br>
            
            <label for="publisher">Publisher:</label>
            <input type="text" name="publisher" placeholder="Publisher"><br>
            <label for="publishercity">Publisher city:</label>
            <input type="text" name="publishercity" placeholder="Publisher city"><br>
            <label for="isbn">ISBN:</label>
            <input type="number" name="isbn" placeholder="ISBN"><br><br>

            <label for="authorfirstname">Author's first name:</label>
            <input type="text" name="authorfirstname" placeholder="Author's first name"><br>
            <label for="authormiddlename">Author's middle name(s):</label>
            <input type="text" name="authormiddlename" placeholder="Author's middle name(s)"><br>
            <label for="authorlastname">Author's last name:</label>
            <input type="text" name="authorlastname" placeholder="Author's last name"><br><br><br>

            <input type="submit" value="Submit">
        </form>
        <br><br>
        <a href="library.php?project_name=<?php echo $_GET["project_name"] ?>&project_id=<?php echo $_GET["project_id"] ?>">Back to <?php echo $_GET["project_name"] ?></a>
    </body>
</html>