<?php

session_start();

/* Remove all session variables */
$_SESSION = array();

/* Destroy the session */
session_destroy();

/* Prevent browser from showing cached dashboard */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* Go back to login page */
header("Location: index.php");
exit();

?>