<?php

session_start();

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$login = trim($_POST["login"] ?? "");
$password = $_POST["password"] ?? "";

if (empty($login) || empty($password)) {

    $_SESSION["error"] =
        "Please enter your username/email and password.";

    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare(
    "SELECT id, full_name, username, email, password
     FROM users
     WHERE username = ? OR email = ?
     LIMIT 1"
);

$stmt->bind_param(
    "ss",
    $login,
    $login
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];

        $stmt->close();
        $conn->close();

        header("Location: dashboard.php");
        exit();
    }
}

$_SESSION["error"] =
    "Incorrect username/email or password.";

$stmt->close();
$conn->close();

header("Location: index.php");
exit();

?>  