<?php

session_start();

require_once "db.php";

/* Check if user is logged in */

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}


/* Get logged-in user's ID */

$user_id = $_SESSION["user_id"];


/* Delete account */

$stmt = $conn->prepare(
    "DELETE FROM users WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);


if ($stmt->execute()) {

    /* Destroy session */

    $_SESSION = array();

    session_destroy();

    /* Prevent browser caching */

    header(
        "Cache-Control: no-store, no-cache, must-revalidate, max-age=0"
    );

    header(
        "Cache-Control: post-check=0, pre-check=0",
        false
    );

    header("Pragma: no-cache");


    /* Redirect to login */

    header(
        "Location: index.php?deleted=1"
    );

    exit();

} else {

    echo "Unable to delete account.";

}


$stmt->close();

$conn->close();

?>