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
}

// Select tasks.task_ID, tasks.title, project.project_title, tasks.due_date, tasks.description, tasks.progress from tasks and join with project on project_ID where tasks.user_ID = $_SESSION["user_ID"] and tasks.progress = 0
$sql = "SELECT tasks.task_ID, tasks.title, project.project_title, tasks.due_date, tasks.description, tasks.progress FROM tasks LEFT JOIN project ON tasks.project_ID = project.project_ID WHERE tasks.user_ID = " . $_SESSION["user_ID"] . " AND tasks.progress = 0";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    $noIncompleteTasks = true;
}
$incompleteTasks = array();
while ($row = mysqli_fetch_assoc($result)) {
    $incompleteTasks[] = $row;
}
// Select completed tasks
$sql = "SELECT tasks.task_ID, tasks.title, project.project_title, tasks.due_date, tasks.description, tasks.progress FROM tasks LEFT JOIN project ON tasks.project_ID = project.project_ID WHERE tasks.user_ID = " . $_SESSION["user_ID"] . " AND tasks.progress = 1";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    $noCompleteTasks = true;
}
$completedTasks = array();
while ($row = mysqli_fetch_assoc($result)) {
    $completedTasks[] = $row;
}

?>

    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Incomplete</button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show">
            <div class="accordion-body row flex-column flex-md-row">
                <?php if ($noIncompleteTasks) { ?>
                    <h3>No incomplete tasks. Well done!</h3>
                    <?php } else {
                    foreach ($incompleteTasks as $task) { ?>
                        <div class="col col-md-3 mb-3 mb-sm-0">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h4 class="card-title"><?php echo $task["title"]; ?></h4>
                                    <h6><?php echo $task["project_title"]; ?></h6>
                                    <h6 class="card-subtitle mb-2 text-secondary">Ongoing: Due <?php echo $task["due_date"]; ?></h6>
                                    <p class="card-text"><?php echo $task["description"]; ?></p>
                                    <button type="submit" class="btn btn-primary" onclick="completeTask(<?php echo $task["task_ID"]; ?>)">Mark as Complete</button>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Completed</button>
            </h2>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse">
            <div class="accordion-body row flex-column flex-md-row">
                <?php if ($noCompleteTasks) { ?>
                    <h3>No completed tasks.</h3>
                    <?php } else {
                    foreach ($completedTasks as $task) { ?>
                        <div class="col col-md-3 mb-3 mb-sm-0">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h4 class="card-title"><?php echo $task["title"]; ?></h4>
                                    <h6><?php echo $task["project_title"]; ?></h6>
                                    <h6 class="card-subtitle mb-2 text-success">Completed</h6>
                                    <p class="card-text"><?php echo $task["description"]; ?></p>
                                    <button type="submit" class="btn btn-primary">Mark as Incomplete</button>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
            </div>
        </div>
    </div>