<?php
session_start();
require_once "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit();
}

$post_id = isset($_POST["post_id"]) ? intval($_POST["post_id"]) : 0;
$reaction = isset($_POST["reaction"]) ? trim($_POST["reaction"]) : "";
$user_id = intval($_SESSION["user_id"]);

$allowed = ["like", "love", "haha", "wow", "sad", "angry"];

if ($post_id <= 0 || !in_array($reaction, $allowed, true)) {
    echo json_encode(["success" => false, "message" => "Invalid reaction"]);
    exit();
}

/* Make sure the post exists. */
$check = $conn->prepare("SELECT id FROM posts WHERE id = ?");
$check->bind_param("i", $post_id);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if (!$exists) {
    echo json_encode(["success" => false, "message" => "Post not found"]);
    exit();
}

/*
  One reaction per user per post.
  Clicking the same reaction removes it.
  Clicking another reaction changes it.
*/
$find = $conn->prepare(
    "SELECT id, reaction
     FROM post_reactions
     WHERE post_id = ? AND user_id = ?"
);
$find->bind_param("ii", $post_id, $user_id);
$find->execute();
$current = $find->get_result()->fetch_assoc();
$find->close();

if ($current) {
    if ($current["reaction"] === $reaction) {
        $stmt = $conn->prepare("DELETE FROM post_reactions WHERE id = ?");
        $stmt->bind_param("i", $current["id"]);
        $stmt->execute();
        $stmt->close();
        $user_reaction = null;
    } else {
        $stmt = $conn->prepare(
            "UPDATE post_reactions SET reaction = ? WHERE id = ?"
        );
        $stmt->bind_param("si", $reaction, $current["id"]);
        $stmt->execute();
        $stmt->close();
        $user_reaction = $reaction;
    }
} else {
    $stmt = $conn->prepare(
        "INSERT INTO post_reactions (post_id, user_id, reaction)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iis", $post_id, $user_id, $reaction);
    $stmt->execute();
    $stmt->close();
    $user_reaction = $reaction;
}

$countStmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM post_reactions WHERE post_id = ?"
);
$countStmt->bind_param("i", $post_id);
$countStmt->execute();
$countRow = $countStmt->get_result()->fetch_assoc();
$countStmt->close();

$icons = [
    "like" => "👍",
    "love" => "❤️",
    "haha" => "😂",
    "wow" => "😮",
    "sad" => "😢",
    "angry" => "😡"
];

echo json_encode([
    "success" => true,
    "count" => intval($countRow["total"]),
    "user_reaction" => $user_reaction,
    "user_reaction_icon" => $user_reaction ? $icons[$user_reaction] : "👍"
]);

$conn->close();
?>



