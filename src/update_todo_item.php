<?php
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