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
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

    .card {
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
    }
</style>

<script>
    function getTasks() {
        $.post("get_tasks_e.php", function(response) {
            $(".accordion-item").html(response);
            setDarkMode();
        });
    }

    function setDarkMode() {
        <?php if ($colour == "text-light bg-dark") { ?>
            $("*").each(function() {
                if ($(this).hasClass("no-dark") == false) {
                    $(this).addClass("text-light bg-dark");
                }
            });
        <?php } ?>
    }

    function completeTask(taskID) {
        $.post("complete_task.php", {
            task_ID: taskID,
            progress: 1
        }, function(response) {
            getTasks();
        });
    }
    function uncompleteTask(taskID) {
        $.post("complete_task.php", {
            task_ID: taskID,
            progress: 0
        }, function(response) {
            getTasks();
        });
    }
    $(document).ready(() => {
        setDarkMode();
        getTasks();
    })
</script>

<body>
    <div class="min-vh-100">
        <main class="container">
            <h1 class="my-5">Dashboard</h1>
            <div class="accordion-item"></div>
            <h1 class="my-5">Todo List</h1>
            <div class="d-flex flex-column flex-md-row py-md-10 align-items-center justify-content-center" style="width:100%;">
                <div class="list-group" style="width:100%;">
                    <label class="list-group-item d-flex gap-3">
                        <input class="form-check-input flex-shrink-0" type="checkbox" value="" checked="" style="font-size: 1.375em;">
                        <span class="pt-1 form-checked-content">
                            <strong>Check Topic Screen</strong>
                            <small class="d-block text-body-secondary">
                                <svg class="bi me-1" width="1em" height="1em">
                                    <use xlink:href="#calendar-event"></use>
                                </svg>
                                1:00–2:00pm
                            </small>
                        </span>
                    </label>
                    <label class="list-group-item d-flex gap-3">
                        <input class="form-check-input flex-shrink-0" type="checkbox" value="" style="font-size: 1.375em;">
                        <span class="pt-1 form-checked-content">
                            <strong>Develop Task 1</strong>
                            <small class="d-block text-body-secondary">
                                <svg class="bi me-1" width="1em" height="1em">
                                    <use xlink:href="#calendar-event"></use>
                                </svg>
                                2:00–2:30pm
                            </small>
                        </span>
                    </label>
                    <label class="list-group-item d-flex gap-3">
                        <input class="form-check-input flex-shrink-0" type="checkbox" value="" style="font-size: 1.375em;">
                        <span class="pt-1 form-checked-content">
                            <strong>Out of office</strong>
                            <small class="d-block text-body-secondary">
                                <svg class="bi me-1" width="1em" height="1em">
                                    <use xlink:href="#alarm"></use>
                                </svg>
                                Tomorrow
                            </small>
                        </span>
                    </label>
                    <label class="list-group-item d-flex gap-3 bg-body-tertiary">
                        <input class="form-check-input form-check-input-placeholder bg-body-tertiary flex-shrink-0 pe-none" disabled="" type="checkbox" value="" style="font-size: 1.375em;">
                        <span class="pt-1 form-checked-content">
                            <span contenteditable="true" class="w-100">Add new task...</span>
                            <small class="d-block text-body-secondary">
                                <svg class="bi me-1" width="1em" height="1em">
                                    <use xlink:href="#list-check"></use>
                                </svg>
                                Specify Time
                            </small>
                        </span>
                    </label>
                </div>
            </div>
        </main>
    </div>
</body>