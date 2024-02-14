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
    if (!isset($_POST["task_ID"])) {
        trigger_error("No task_ID in POST request.");
    }
    if (!isset($_POST["progress"])) {
        trigger_error("No progress in POST request.");
    }
    if ($_POST["progress"] != 1 && $_POST["progress"] != 0 && $_POST["progress"] != 2) {
        trigger_error("Invalid progress in POST request.");
    }
    $task_ID = $_POST["task_ID"];
    $progress = $_POST["progress"];
    $sql = "UPDATE tasks SET progress = " . $progress . " WHERE task_ID = " . $task_ID;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        trigger_error("Error updating task progress.");
    }
}