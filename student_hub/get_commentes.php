<?php
session_start();
require_once "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit();
}

$post_id = isset($_GET["post_id"]) ? intval($_GET["post_id"]) : 0;

if ($post_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid post"]);
    exit();
}

/*
 Displaying Records: The fetched database rows are processed, encoded in JSON format,
 and dynamically rendered onto the dashboard interface for users to view posts, comments, and reaction lists
*/
$stmt = $conn->prepare(
    "SELECT pc.comment, pc.created_at, u.full_name
     FROM post_comments pc
     LEFT JOIN users u ON u.id = pc.user_id
     WHERE pc.post_id = ?
     ORDER BY pc.created_at ASC"
);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database prepare failed"]);
    exit();
}

$stmt->bind_param("i", $post_id);
$stmt->execute();

$result = $stmt->get_result();
$comments = [];

while ($row = $result->fetch_assoc()) {
    $comments[] = [
        "comment" => htmlspecialchars($row["comment"], ENT_QUOTES, "UTF-8"),
        "full_name" => htmlspecialchars($row["full_name"] ?? "User", ENT_QUOTES, "UTF-8"),
        "created_at" => $row["created_at"]
    ];
}

echo json_encode([
    "success" => true,
    "comments" => $comments
]);

$stmt->close();
$conn->close();
?>