<?php

session_start();

require_once "db.php";


/* =========================================
1.	CREATE – SQL Code and Explanation (20 pts)

Form & Modal Design:  The interface provides a form and modal pop-up on `dashboard.php` where users can type post content (such as "HELLO WORLD!")
Input Validationn `create_post.php , the script checks if the user's session is active (`$_SESSION["user_id"]`), ensures the request method is POST,
 and verifies that the input text is not empty or exceeding the 1000-character limit
Database Insertion: To save this into the database, an `INSERT INTO posts` SQL query is executed using the captured
 user input and session ID, successfully adding the new record and redirecting back with a success message (`?posted=1`).

========================================= */


/* =========================================
   CHECK IF USER IS LOGGED IN
========================================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: index.php");

    exit();

}


/* =========================================
   ONLY ACCEPT POST REQUEST
========================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: dashboard.php");

    exit();

}


/* =========================================
   GET POST DATA
========================================= */

$user_id = $_SESSION["user_id"];

$content = trim($_POST["content"] ?? "");


/* =========================================
   CHECK EMPTY POST
========================================= */

if ($content === "") {

    header("Location: dashboard.php?error=empty");

    exit();

}


/* =========================================
   LIMIT POST LENGTH
========================================= */

if (strlen($content) > 1000) {

    header("Location: dashboard.php?error=long");

    exit();

}


/* =========================================
   SAVE POST TO DATABASE
========================================= */

$stmt = $conn->prepare(
    "INSERT INTO posts (user_id, content)
     VALUES (?, ?)"
);

$stmt->bind_param(
    "is",
    $user_id,
    $content
);


if ($stmt->execute()) {

    $stmt->close();  

    $conn->close();

    header("Location: dashboard.php?posted=1");

    exit();

}


/* =========================================
   ERROR
========================================= */

$stmt->close();

$conn->close();

header("Location: dashboard.php?error=failed");

exit();

?>