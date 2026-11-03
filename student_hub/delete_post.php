<?php

session_start();

require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if post ID was submitted
if (!isset($_POST['post_id'])) {
    header("Location: dashboard.php");
    exit();
}

$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['user_id'];

// Delete only the user's own post
$sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);

if (mysqli_stmt_execute($stmt)) {

    header("Location: dashboard.php");
    exit();

} else {

    echo "Error deleting post: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>