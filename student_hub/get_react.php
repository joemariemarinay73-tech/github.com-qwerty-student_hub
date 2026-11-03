<?php
session_start();
require_once "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit();
}

$post_id = isset($_GET["post_id"]) ? intval($_GET["post_id"]) : 0;
$user_id = intval($_SESSION["user_id"]);

if ($post_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid post"]);
    exit();
}

$countStmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM post_reactions WHERE post_id = ?"
);
$countStmt->bind_param("i", $post_id);
$countStmt->execute();
$countRow = $countStmt->get_result()->fetch_assoc();
$countStmt->close();

$userStmt = $conn->prepare(
    "SELECT reaction FROM post_reactions
     WHERE post_id = ? AND user_id = ?"
);
$userStmt->bind_param("ii", $post_id, $user_id);
$userStmt->execute();
$userRow = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

$icons = [
    "like" => "👍",
    "love" => "❤️",
    "haha" => "😂",
    "wow" => "😮",
    "sad" => "😢",
    "angry" => "😡"
];

$user_reaction = $userRow["reaction"] ?? null;

echo json_encode([
    "success" => true,
    "count" => intval($countRow["total"]),
    "user_reaction" => $user_reaction,
    "user_reaction_icon" => $user_reaction ? $icons[$user_reaction] : "👍"
]);

$conn->close();
?>