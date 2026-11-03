<?php
session_start();
require_once "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit();
}

if (!isset($_POST["post_id"], $_POST["comment"])) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit();
}

$post_id = intval($_POST["post_id"]);
$user_id = intval($_SESSION["user_id"]);
$comment = trim($_POST["comment"]);

if ($post_id <= 0 || $comment === "") {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit();
}

if (strlen($comment) > 500) {
    echo json_encode(["success" => false, "message" => "Comment is too long"]);
    exit();
}

$stmt = $conn->prepare(
    "INSERT INTO post_comments (post_id, user_id, comment)
     VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database prepare failed"]);
    exit();
}

$stmt->bind_param("iis", $post_id, $user_id, $comment);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "comment" => htmlspecialchars($comment, ENT_QUOTES, "UTF-8")
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add comment"]);
}

$stmt->close();
$conn->close();
?>n