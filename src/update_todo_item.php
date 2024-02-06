<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_error_handler("handleError");
function handleError($errno, $errstr)
{
    echo "<b>Error:</b> [$errno] $errstr";
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
    if (!isset($_POST["item_ID"])) {
        trigger_error("No item_ID in POST request.");
    }
    if (!isset($_POST["status"])) {
        trigger_error("No status in POST request.");
    }
    if ($_POST["status"] != 1 && $_POST["status"] != 0) {
        trigger_error("Invalid status in POST request.");
    }
    $item_ID = $_POST["item_ID"];
    $status = $_POST["status"];
    $sql = "UPDATE ToDoItems SET status = " . $status . " WHERE item_ID = " . $item_ID;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        trigger_error("Error updating task progress.");
    }
}