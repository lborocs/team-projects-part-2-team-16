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
// Select count of number of tasks with progress 1 and count total number (including uncompleted) of tasks, due_date group by project_ID, join project on project_ID and get the project_title and due_date and first 15 characters of description if exists
$sql = "SELECT project.project_ID, project_title, project.due_date, LEFT(project.description, 15) as description, COUNT(CASE WHEN progress = 1 THEN 1 END) as completed_tasks, COUNT(progress) as total_tasks FROM project LEFT JOIN tasks ON tasks.project_ID = project.project_ID GROUP BY project.project_ID";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    echo "No projects found.";
    die();
}
$array = array();
while ($row = mysqli_fetch_assoc($result)) {
    $array[] = $row;
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
    $(document).ready(function() {
        <?php if ($colour == "text-light bg-dark") { ?>
            $("*").each(function() {
                if ($(this).hasClass("no-dark") == false) {
                    $(this).addClass("text-light bg-dark");
                }
            });
        <?php } ?>
    })
</script>

<body>
    <div class="min-vh-100">
        <main class="container">
            <div class="row py-4">
                <div class="col">
                    <h1>Dashboard</h1>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-end">
                        <a href="./create_project.php" class="btn btn-primary">Create New Project</a>
                    </div>
                </div>
            </div>

            <div class="row flex-column flex-md-row">
                <?php foreach ($array as $project) { ?>
                    <div class="col col-md-3 mb-3 mb-sm-0">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <?php echo $project["project_title"]; ?>
                                </h4>
                                <!-- Convert date into number of days and display as subtitle -->
                                <h6 class="card-subtitle mb-2 text-secondary no-dark">Due on
                                    <?php echo $project["due_date"]; ?>
                                </h6>
                                <?php if ($project["total_tasks"] > 0) {
                                    $completionPercentage = $project["completed_tasks"] / $project["total_tasks"] * 100;
                                } else {
                                    $completionPercentage = 0;
                                }
                                ?>
                                <div class="progress border no-dark" role="progressbar" aria-label="Basic example" aria-valuenow="<?php echo $completionPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar no-dark" style="width: <?php echo $completionPercentage; ?>%"></div>
                                </div>
                                <p class="card-text">
                                    <?php echo $project["description"]; ?>
                                </p>
                                <a href="view_team.php?project_ID=<?php echo $project["project_ID"]; ?>" class="btn btn-primary stretched-link">View</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </main>
    </div>
</body>




<!-- <div class="col col-md-3 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Team 1</h4>
                            <h6 class="card-subtitle mb-2 text-secondary">Due in 6 days</h6>
                            <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="75"
                                aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: 75%"></div>
                            </div>
                            <p class="card-text">Team 1's progress</p>
                            <a href="view_team_m.html" class="btn btn-primary stretched-link">View</a>
                        </div>
                    </div>
                </div>
                <div class="col col-md-3 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Team 2</h4>
                            <h6 class="card-subtitle mb-2 text-secondary">Due in 2 weeks</h6>
                            <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="20"
                                aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: 20%"></div>
                            </div>
                            <p class="card-text">Team 2's progress</p>
                            <a href="view_team_m.html" class="btn btn-primary stretched-link">View</a>
                        </div>
                    </div>
                </div>
                <div class="col col-md-3 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Team 3</h4>
                            <h6 class="card-subtitle mb-2 text-secondary">Due in 5 weeks</h6>
                            <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="62"
                                aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: 62%"></div>
                            </div>
                            <p class="card-text">Team 3's progress</p>
                            <a href="view_team_m.html" class="btn btn-primary stretched-link">View</a>
                        </div>
                    </div>
                </div>
            </div> -->