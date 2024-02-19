<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
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
        $.post("get_tasks", {
            user_ID: <?php echo $_SESSION["user_ID"] ?>
        }, function(response) {
            try {
                response = JSON.parse(response);
            } catch (error) {
                alert(response);
            }
            var incompleteTasks = response["incompleteTasks"];
            if (incompleteTasks.length == 0) {
                $("#incomplete").html("<h3>All tasks complete. Well done!</h3>");
            } else {
                var html = "";
                for (var i = 0; i < incompleteTasks.length; i++) {
                    var task = incompleteTasks[i];
                    html += "<div class='col col-md-3 mb-3 mb-sm-0'>";
                    html += "<div class='card mb-3'>";
                    html += "<div class='card-body'>";
                    html += "<h4 class='card-title'>" + task["title"] + "</h4>";
                    html += "<h6>" + task["project_title"] + "</h6>";
                    if (task["progress"] == 0) {
                        html += "<h6 class='card-subtitle mb-2 text-secondary'>Not Started: Due " + task["due_date"] + "</h6>";
                    } else if (task["progress"] == 1) {
                        html += "<h6 class='card-subtitle mb-2 text-secondary'>Ongoing: Due " + task["due_date"] + "</h6>";
                    }
                    html += "<p class='card-text'>" + task["description"] + "</p>";
                    if (task["progress"] == 0) {
                        html += "<button type='button' class='btn btn-xs' onclick='startTask(" + task["task_ID"] + ")'><i class='bi-play-fill' style='font-size: 2em; color: #0d6efd;'></i></button>";
                    } else if (task["progress"] == 1) {
                        html += "<button type='button' class='btn btn-xs' onclick='uncompleteTask(" + task["task_ID"] + ")'><i class='bi-stop-fill' style='font-size: 2em; color: #0d6efd;'></i></button></button>";
                    }
                    html += "<button type='submit' class='btn btn-primary' onclick='completeTask(" + task["task_ID"] + ")'>Mark as Complete</button>";
                    html += "</div>";
                    html += "</div>";
                    html += "</div>";
                }
                $("#incomplete").html(html);
            }
            var completedTasks = response["completedTasks"];
            if (completedTasks.length == 0) {
                $("#complete").html("<h3>No completed tasks.</h3>");
            } else {
                var html = "";
                for (var i = 0; i < completedTasks.length; i++) {
                    var task = completedTasks[i];
                    html += "<div class='col col-md-3 mb-3 mb-sm-0'>";
                    html += "<div class='card mb-3'>";
                    html += "<div class='card-body'>";
                    html += "<h4 class='card-title'>" + task["title"] + "</h4>";
                    html += "<h6>" + task["project_title"] + "</h6>";
                    html += "<h6 class='card-subtitle mb-2 text-success'>Completed</h6>";
                    html += "<p class='card-text'>" + task["description"] + "</p>";
                    html += "<button type='submit' class='btn btn-primary' onclick='uncompleteTask(" + task["task_ID"] + ")'>Mark as Incomplete</button>";
                    html += "</div>";
                    html += "</div>";
                    html += "</div>";
                }
                $("#complete").html(html);
            }
            setDarkMode();
        });
    }

    // Add dark mode class if dark mode is enabled
    function setDarkMode() {
        <?php if ($colour == "text-light bg-dark") { ?>
            $("*").each(function() {
                if ($(this).hasClass("no-dark") == false && $(this).parents("header").length == 0) {
                    $(this).addClass("text-light bg-dark");
                }
            });
        <?php } ?>
    }

    function startTask(taskID) {
        $.post("change_task_progress.php", {
            task_ID: taskID,
            progress: 1
        }, function(response) {
            getTasks();
        });
    }

    function completeTask(taskID) {
        $.post("change_task_progress.php", {
            task_ID: taskID,
            progress: 2
        }, function(response) {
            getTasks();
        });
    }

    function uncompleteTask(taskID) {
        $.post("change_task_progress.php", {
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
    <!-- Placeholder html to be AJAX'd into -->
    <div class="min-vh-100">
        <main class="container">
            <h1 class="my-5">Dashboard</h1>
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Incomplete</button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show">
                        <div id="incomplete" class="accordion-body row flex-column flex-md-row">
                            <h3>All tasks complete. Well done!</h3>
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
                        <div id="complete" class="accordion-body row flex-column flex-md-row">
                            <h3>No completed tasks.</h3>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "todo_list.php"; ?>
        </main>
    </div>
</body>