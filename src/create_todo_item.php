<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_error_handler("handleError");
function handleError($errno, $errstr)
{
    echo "<script>Console.log('Error: [$errno] $errstr')</script>";
    die();
}
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    echo "Connection Error.";
    die();
}
if (!$_POST) {
    trigger_error("No POST request.");
} else {
    if (!isset($_POST["user_ID"])) {
        trigger_error("No user_ID in POST request.");
    }
    if (!isset($_POST["title"])) {
        trigger_error("No title in POST request.");
    }
    if (!isset($_POST["due_date"])) {
        trigger_error("No due_date in POST request.");
    }
    $user_ID = $_POST["user_ID"];
    $title = $_POST["title"];
    $due_date = $_POST["due_date"];
}

// Create new todo item with status 0
$sql = "INSERT INTO ToDoItems (user_ID, title, due_date, status) VALUES (?, ?, ?, 0)";
$params = array($user_ID, $title, $due_date);
$result = mysqli_execute_query($conn, $sql, $params);
if (!$result) {
    trigger_error("Error creating new todo item.");
}