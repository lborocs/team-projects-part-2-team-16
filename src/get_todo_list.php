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
    $user_ID = $_POST["user_ID"];
}

// Select ToDoItems.item_ID, ToDoItems.title, ToDoItems.due_date, ToDoItems.status from ToDoItems where ToDoItems.user_ID = $user_ID
$sql = "SELECT ToDoItems.item_ID, ToDoItems.title, ToDoItems.due_date, ToDoItems.status FROM ToDoItems WHERE ToDoItems.user_ID = " . $user_ID;
$result = mysqli_query($conn, $sql);

$toDoList = array();
while ($row = mysqli_fetch_assoc($result)) {
    $toDoList[] = $row;
}

echo json_encode($toDoList);