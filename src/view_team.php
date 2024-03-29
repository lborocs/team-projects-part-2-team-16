<?php
session_start();
if ($_SESSION["role"] == "TL") {
	if (isset($_GET["project_ID"])) {		//TL leads many teams, a team was selected
		//connect to database
		try {
			include "db_connection.php";
			$conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
		} catch (PDOException $e) {
			echo "<script>alert('Failed to connect to database');</script>";
			exit();
		}
		//check if the user leads the given team
		
		$currentProjectID = $_GET["project_ID"];
		echo "<script>console.log('is this running');</script>";
		$result = $conn->query("SELECT team_leader FROM project WHERE project_ID = $currentProjectID");
		$queryResult = $result->fetch(PDO::FETCH_NUM);
		if (!$queryResult) {
			echo "<script>alert('Project not found');</script>";
			exit();
		}
		$selectedTL = $queryResult[0];
		if (!$selectedTL == $_SESSION["user_ID"]) {
			echo "<script>alert('You do not have permission to view this project');</script>";
			exit();
		}
		$taskcreate = "link-dark";
		$topicview = "link-dark";
		$dashview = "link-dark";
		$taskview = "link-dark";
		include "./navbar_tl.php";
	} elseif (isset($numOfProjectLeads)) {	//has been called from dashboard.php, TL leads 1 team
		$result = $conn->query("SELECT project_ID FROM project WHERE team_leader = ".$_SESSION["user_ID"]);
		$currentProjectID = $result->fetch(PDO::FETCH_NUM)[0];
	} else {	//user removed GET data from URL
		echo "<script>alert('No project was specified');</script>";
		exit();
	}
} elseif ($_SESSION["role"] == "Manager" and isset($_GET["project_ID"])) {
	//connect to database
	try {
		include "db_connection.php";
		$conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
	} catch (PDOException $e) {
		echo "<script>alert('Failed to connect to database');</script>";
		exit();
	}
	$currentProjectID = $_GET["project_ID"];
	//check if the given project exists
	$result = $conn->query("SELECT team_leader FROM project WHERE project_ID = $currentProjectID;");
	$queryResult = $result->fetch(PDO::FETCH_NUM);
	if (!$queryResult) {
		echo "<script>alert('Project not found');</script>";
		exit();
	}
	$taskcreate = "link-dark";
	$topicview = "link-dark";
	$dashview = "link-dark";
	include "./navbar_m.php";
} else {
	echo "<script>alert('Invalid page access');</script>";
	exit();
}

//gets the information of the selected project
$result = $conn->query("SELECT project_title, due_date, description, forename, surname FROM project, users where project_ID = $currentProjectID and users.user_ID = project.team_leader");
if (!$result) {
	echo "<script>alert('Failed to connect to database');</script>";
	exit;
}
$projectData = $result->fetch(PDO::FETCH_ASSOC);

//adds task to database if assign task was used using this page
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	include "add_task.php";
	$projectID = intval($currentProjectID);
	create_task($projectID, $conn);
}

//gets the list of tasks for the returned project, orders tasks first by user ID to group user tasks together,
//then by task progress in order of: in progress, incomplete, complete
$result = $conn->query("SELECT tasks.task_ID,tasks.user_ID,users.forename,users.surname,tasks.title,tasks.description,
		tasks.due_date,tasks.est_hours,tasks.progress FROM tasks INNER JOIN users ON tasks.user_ID = users.user_ID 
		WHERE tasks.project_ID = " . $currentProjectID . " ORDER BY tasks.user_ID, (CASE progress WHEN 1 THEN 1 WHEN 0 THEN 2 ELSE 3 END)");
if (!$result) {
	echo "<script>alert('Failed to connect to database');</script>";
	exit;
}
$taskArray = $result->fetchAll(PDO::FETCH_NUM);

//counts the number of tasks assigned to team + individuals, and how many tasks completed by team + individuals
$teamCompletedTasks = 0;
$totalTeamTasks = count($taskArray);
$currentUserID = -1;
$usersProgress = [];			//array for storing [completed tasks, total tasks]
foreach ($taskArray as $task) {
	if ($task[1] != $currentUserID) {		//if current task is for a new team member
		$currentUserID = $task[1];
		$usersProgress[$currentUserID] = [0, 0];
	}
	$usersProgress[$currentUserID][1]++;
	if ($task[8] == 2) {						//if task is complete
		$teamCompletedTasks++;
		$usersProgress[$currentUserID][0]++;
	}
}
if ($totalTeamTasks == 0) {
	$teamProgress = 0;
} else {
	$teamProgress = (($teamCompletedTasks / $totalTeamTasks) * 100);
}
?>
<html>

<head>
	<style>
		#page-content {
			margin-bottom: 12rem;
		}

		#page-header {
			padding: 2rem 1rem;
			margin-bottom: 1rem;
		}

		/* #project-desc, #project-date {
			
		} */

		.accordion-button:not(.collapsed) {
			background-color: white;
			color: #212529;
			box-shadow: none;
		}

		.accordion-button:focus {
			box-shadow: none;
			border-color: rgba(0, 0, 0, .125);
		}

		.memberprogress {
			position: absolute;
			right: 100px;
			top: 50%;
			transform: translate(0, -50%);
		}

		.progress-text {
			position: absolute;
			right: 50px;
			top: 50%;
			transform: translate(0, -50%);
			font-weight: normal;
		}

		.taskcard {
			min-height: 10rem;
		}

		.horizontal-scroll {
			overflow-x: auto;
			padding-bottom: 1%;
		}

		.task-completed {
			box-shadow: 0px 0px 15px #b2ffa8;
			margin: 2%;
			border: 2px solid #b2ffa8;
		}

		.task-in-progress {
			box-shadow: 0px 0px 15px #ffce85;
			border: 2px solid #ffce85;
			margin: 2%;
		}

		.task-incomplete {
			box-shadow: 0px 0px 15px #ffa8a8;
			border: 2px solid #ffa8a8;
			margin: 2%;
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script>
	$(document).ready(function() {
		<?php if ($colour == "text-light bg-dark") { ?>
			$("*").each(function() {
				if ($(this).hasClass("no-dark") == false && $(this).parents("header").length == 0) {
					$(this).addClass("text-light bg-dark");
				}
			});
		<?php } ?>
	})
	</script>
</head>

<body>
	<div class="container mt-3" id="page-content">
		<div id="page-header">
			<div class="d-flex" style="align-items:center;">
				<div class="p-2">
					<h1><strong><?php echo $projectData["project_title"]; ?></strong></h1>
				</div>
				<div class="flex-grow-1 p-2">
					<div class="progress no-dark" style="width: 28%; height: 10px; float:right;">
						<div class="progress-bar no-dark" role="progressbar" <?php echo 'style="width: '.$teamProgress.'%;" aria-valuenow="'.$teamProgress.'"';
																		//progress bar for team
																		?> aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
				<div class="p-2">
					<small class="text-muted" style="float:right;"><?php echo $teamCompletedTasks . ' of ' . $totalTeamTasks; ?></small>
				</div>
			</div>
		</div>
		<div style="display: none; padding: 0rem 1.5rem;" id="project_details">
			<div><?php echo "Due: ".$projectData["due_date"]; ?></div>
			<div><?php echo "Team Leader: ".$projectData["forename"] . " " . $projectData["surname"]; ?></div>
			<hr>
			<div class="mb-5" id="project-desc">
				<?php echo $projectData["description"]; ?>
			</div>
			<hr>
		</div>
		<p id="showDetails" style="color: #636b74; padding: 0rem 1.5rem;" type="button" onclick="showdetails()">show project details...</p>

		<div class="accordion">
			<?php
			if ($totalTeamTasks == 0) {
				echo "<p style='text-align:center;margin: 5rem 2rem'>This project does not have any tasks.</p>";
			}
			$numOfUsersAdded = 0;
			$currentUserID = 0;
			foreach ($taskArray as $task) {
				if ($task[1] != $currentUserID) {					//if current task is for a new team member
					$currentUserID = $task[1];
					if ($currentUserID != $taskArray[0][1]) {		//if current team member is not the first team member in the team
						$numOfUsersAdded++;
						echo '</div></div></div></div></div>';		//ends accordion item for previous team member
					}
					//creates the accordion header for the current user	
					echo '<div class="accordion-item">
						<h2 class="accordion-header">
							<div class="accordion-button" id="accordion-button-' . $numOfUsersAdded . '" onclick="toggleAccordion(' . $numOfUsersAdded . ')">
								<div class="row flex-md-nowrap align-items-center">
									<div class="col-md-9" type="button">
										<h2>' . $task[2] . ' ' . $task[3] . '</h2>
									</div>
									<div class="col-md-6">
										<div class="dropdown">
											<button type="button" class="btn btn-primary dropdown-toggle quick-assign-task"
												data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"
												onclick="stopProp(event)">+ Assign task </button>
											<form method="post" action="" class="dropdown-menu p-4 pt-3" style="width:256;" onclick="stopProp(event)">
												<input type="hidden" value=' . $currentUserID . ' name="employee">
												<div class="mb-2">
													<label for="tasktitle-' . $numOfUsersAdded . '" class="form-label">Task Title</label>
													<input type="text" class="form-control" id="tasktitle-' . $numOfUsersAdded . '" name="title" required>
												</div>
												<div class="mb-2">
													<label for="taskdesc-' . $numOfUsersAdded . '" class="form-label">Task Description</label>
													<textarea class="form-control" id="taskdesc-' . $numOfUsersAdded . '" name="description" required></textarea>
												</div>
												<div class="mb-2">
													<label for="manhours-' . $numOfUsersAdded . '" class="form-label">Estimated Man Hours</label>
													<input type="number" class="form-control" id="manhours-' . $numOfUsersAdded . '" name="manhours" required>
												</div>
												<div class="mb-3">
													<label for="duedate-' . $numOfUsersAdded . '" class="form-label">Due Date</label>
													<input type="date" class="form-control" id="duedate-' . $numOfUsersAdded . '" name="date" placeholder="DD/MM/YYYY" required>
												</div>
												<button type="submit" class="btn btn-primary">Assign Task</button>
											</form>
										</div>
									</div>
								</div>
								<div class="progress memberprogress no-dark" style="width: 20%; height: 10px;">
									<div class="progress-bar" role="progressbar no-dark"
										style="width: ' . (($usersProgress[$currentUserID][0] / $usersProgress[$currentUserID][1]) * 100) . '%;"
										aria-valuenow="' . (($usersProgress[$currentUserID][0] / $usersProgress[$currentUserID][1]) * 100) . '"
										aria-valuemin="0" aria-valuemax="100"></div>
								</div>
								<p class="progress-text"><small class="text-muted">' . $usersProgress[$currentUserID][0] . ' of ' . $usersProgress[$currentUserID][1] . '</small></p>
							</div>
						</h2>
						<div id="collapse' . $numOfUsersAdded . '" class="accordion-collapse collapse show">
							<div class="accordion-body pt-0">
								<div class="container-fluid px-0">
									<div class="row flex-md-row horizontal-scroll flex-md-nowrap">';
				}
				//adds task to user's list of tasks
				echo 						'<div class="col-12 col-md-3 mb-3 mb-md-0">
											<div class="card card-body h-100 taskcard ';
				//css class for task
				if ($task[8] == 0) {
					echo 'task-incomplete';
				} else if ($task[8] == 1) {
					echo 'task-in-progress';
				} else {
					echo 'task-completed';
				}
				echo '">
												<a href="./create_task.php?edit_ID=' . $task[0] . '" class="position-absolute top-0 end-0">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="grey" class="bi bi-pencil-square position-absolute top-0 end-0" viewBox="0 0 16 16">
														<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
														<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
													</svg>
												</a>
												<h5 class="card-title">' . $task[4] . '</h5>
												<p class="card-text mb-0">' . $task[5] . '</p>
												<p class="card-text mb-0 mt-auto"><small class="text-muted">Task length: ' . $task[7] . ' hours</small></p>
												<p class="card-text"><small class="text-muted">Due: ' . $task[6] . '</small></p>
											</div>
										</div>';
			}
			echo '</div></div></div></div></div>';		//ends accordion for final team member
			?>
		</div> <?php //closes div class="accordion" 
				?>
	</div> <?php //closes div id="page-content" 
			?>
	<?php include "./footer.php"; ?>
	<script>
		function toggleAccordion(collapseNum) {
			if (!$("#collapse" + collapseNum).hasClass("collapsing")) {
				$("#accordion-button-" + collapseNum).toggleClass("collapsed");
			}
			$("#collapse" + collapseNum).collapse("toggle");
		}

		function stopProp(event) {
			event.stopPropagation();
		}

		function showdetails() {
			showButton = document.getElementById("showDetails");
			if (showButton.innerHTML == "show project details...") {
				document.getElementById("project_details").style.display = "block";
				showButton.innerHTML = "hide project details...";
			} else if (showButton.innerHTML == "hide project details...") {
				document.getElementById("project_details").style.display = "none";
				showButton.innerHTML = "show project details...";
			}
		}
	</script>
</body>

</html>
