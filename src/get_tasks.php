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

// Select tasks.task_ID, tasks.title, project.project_title, tasks.due_date, tasks.description, tasks.progress from tasks and join with project on project_ID where tasks.user_ID = $_SESSION["user_ID"] and tasks.progress = 0
$sql = "SELECT tasks.task_ID, tasks.title, project.project_title, tasks.due_date, tasks.description, tasks.progress FROM tasks LEFT JOIN project ON tasks.project_ID = project.project_ID WHERE tasks.user_ID = " . $user_ID . " AND tasks.progress = 0";
$result = mysqli_query($conn, $sql);
$incompleteTasks = array();
while ($row = mysqli_fetch_assoc($result)) {
    $incompleteTasks[] = $row;
}
// Select completed tasks
$sql = "SELECT tasks.task_ID, tasks.title, project.project_title, tasks.due_date, tasks.description, tasks.progress FROM tasks LEFT JOIN project ON tasks.project_ID = project.project_ID WHERE tasks.user_ID = " . $user_ID . " AND tasks.progress = 1";
$result = mysqli_query($conn, $sql);
$completedTasks = array();
while ($row = mysqli_fetch_assoc($result)) {
    $completedTasks[] = $row;
}

$response = array("incompleteTasks" => $incompleteTasks, "completedTasks" => $completedTasks);
echo json_encode($response);
exit();