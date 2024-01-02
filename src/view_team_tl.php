<?php
	include "db_connection.php";
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	if (!$conn) {
		echo "Connection Error.";
		exit;
	}
	
	// mysql: SELECT * FROM project;
	// +------------+-------------+----------------+------------+
	// | project_ID | team_leader | project_title  | due_date   |
	// +------------+-------------+----------------+------------+
	// |          1 |           1 | test project 1 | 2030-01-01 |
	// |          2 |           1 | test project 2 | 2040-01-01 |
	// |          3 |           1 | test project 3 | 2050-01-01 |
	// +------------+-------------+----------------+------------+
	
	$result = mysqli_query($conn, "SELECT project_ID, project_title, due_date FROM  project where team_leader =".$_SESSION["user_ID"]);
	if (!$result) {
		echo "Connection Error.";
		exit;
	}
	$projectsArray = mysqli_fetch_all($result);
	if (count($projectsArray) > 1) {
		echo "Team leader leads ".count($projectsArray)." teams, not yet implemented. User ID: ".$_SESSION["user_ID"];
		exit;
	}
	
	$currentProjectID = $projectsArray[0][0];
	$result = mysqli_query($conn, "SELECT tasks.task_ID,tasks.user_ID,users.forename,users.surname,tasks.title,tasks.description,tasks.due_date,tasks.est_hours,tasks.progress FROM tasks INNER JOIN users ON tasks.user_ID = users.user_ID WHERE tasks.project_ID = ".$currentProjectID." ORDER BY tasks.user_ID, (CASE progress WHEN 1 THEN 1 WHEN 0 THEN 2 ELSE 3 END);");
	if (!$result) {
		echo "Connection Error.";
		exit;
	}
	$taskArray = mysqli_fetch_all($result);
	// example where current project id = 1:
	// +---------+---------+----------+---------+-------+-------------+------------+-----------+----------+
	// | task_ID | user_ID | forename | surname | title | description | due_date   | est_hours | progress |
	// +---------+---------+----------+---------+-------+-------------+------------+-----------+----------+
	// |       1 |       1 | Harry    | Kane    | title | desc        | 2023-12-27 |         7 |        0 |
	// |       2 |       1 | Harry    | Kane    | title | desc        | 2023-12-27 |         7 |        0 |
	// +---------+---------+----------+---------+-------+-------------+------------+-----------+----------+
?>
<html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/">

	<link href="/docs/5.0/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

	<link rel="apple-touch-icon" href="/docs/5.0/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="/docs/5.0/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="/docs/5.0/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="manifest" href="/docs/5.0/assets/img/favicons/manifest.json">
	<link rel="mask-icon" href="/docs/5.0/assets/img/favicons/safari-pinned-tab.svg" color="#7952b3">
	<link rel="icon" href="/docs/5.0/assets/img/favicons/favicon.ico">
	<meta name="theme-color" content="#7952b3">


	<style>
		/*.bd-placeholder-img {
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
		}*/
		
		#page-content {
			margin-bottom: 12rem;
		}
		
		#page-header {
			padding: 2rem 1rem;
			margin-bottom: 1rem;
		}
		
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
	<link href="./headers.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3" id="page-content">
	<div id="page-header">
		<div class="d-flex" style="align-items:center;">
			<div class="p-2">
				<h1><strong><?php echo $projectsArray[0][1];?></strong></h1>
			</div>
			<div class="flex-grow-1 p-2">
				<div class="progress" style="width: 28%; height: 10px; float:right;">
					<div class="progress-bar" role="progressbar" style="width: 17%;" aria-valuenow="17"
						aria-valuemin="0" aria-valuemax="100"></div>
				</div>
			</div>
			<div class="p-2">
				<small class="text-muted" style="float:right;">2 of 12</small>
			</div>
		</div>
	</div>
	<div class="accordion">
		<?php
		$totalTeamTasks = count($taskArray);
		$teamCompletedTasks = 0;
		$currentUserID = -1;
		foreach ($taskArray as $task) {
			$totalTeamTasks++;
			if ($task[1] != $currentUserID) {					//if current task is for a new team member
				$currentUserID = $task[1];
				$totalUserTasks = 0;
				$tasksCompletedByUser = 0;
				if ($currentUserID != $taskArray[0][1]) {		//if current team member is not the first team member in the team
					echo '</div></div></div></div></div>';		//ends accordion item for previous team member
				}
				// to change in text below: progess bar -----------------------------------------------------------------------------------------------
				// current issues: need to find progress before progress bars are echoed
				echo '<div class="accordion-item">
						<h2 class="accordion-header">
							<div class="accordion-button" onclick="toggleAccordion(\'collapseOne\')">
								<div class="row flex-md-nowrap align-items-center">
									<div class="col-md-9" type="button">
										<h2>'.$task[2].' '.$task[3].'</h2>
									</div>
									<div class="col-md-6">
										<div class="dropdown">
											<button type="button" class="btn btn-primary dropdown-toggle quick-assign-task"
												data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"
												onclick="stopProp(event)">+ Add task </button>
											<form class="dropdown-menu p-4 pt-3" style="width:256;" onclick="stopProp(event)">
												<div class="mb-2">
													<label for="taskname" class="form-label">Task Name</label>
													<input type="text" class="form-control" id="taskname">
												</div>
												<div class="mb-2">
													<label for="taskdesc" class="form-label">Task Description</label>
													<textarea class="form-control" id="taskdesc"></textarea>
												</div>
												<div class="mb-2">
													<label for="manhours" class="form-label">Estimated Man Hours</label>
													<input type="number" class="form-control" id="manhours">
												</div>
												<div class="mb-3">
													<label for="duedate" class="form-label">Due Date</label>
													<input type="text" class="form-control" id="duedate" placeholder="DD/MM/YYYY">
												</div>
												<button type="submit" class="btn btn-primary">Assign Task</button>
											</form>
										</div>
									</div>
								</div>
								<div class="progress memberprogress" style="width: 20%; height: 10px;">
									<div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
										aria-valuemin="0" aria-valuemax="100"></div>
								</div>
								<p class="progress-text"><small class="text-muted">0 of 4</small></p>
							</div>
						</h2>
						<div id="collapseOne" class="accordion-collapse collapse show">
							<div class="accordion-body pt-0">
								<div class="container-fluid px-0">
									<div class="row flex-md-row horizontal-scroll flex-md-nowrap">';
			}
			$totalUserTasks++;
			echo '<div class="col-12 col-md-3 mb-3 mb-md-0">
					<div class="card card-body h-100 taskcard ';
			if ($task[8] == 0) {
				echo 'task-incomplete';
			} else if ($task[8] == 1) {
				echo 'task-in-progress';
			} else {
				echo 'task-completed';
				$teamCompletedTasks++;
				$tasksCompletedByUser++;
			}
			echo '">
						<h5 class="card-title">'.$task[4].'</h5>
						<p class="card-text mb-0">'.$task[5].'</p>
						<p class="card-text mb-0 mt-auto"><small class="text-muted">Task length: '.$task[7].' hours</small></p>
						<p class="card-text"><small class="text-muted">Due: '.$task[6].'</small></p>
					</div>
				</div>';
		}
		echo '</div></div></div></div></div>';
		?>
	  
	  
	</div>
<!-- add to-do list -->
<!-- add footer -->

<script>
function toggleAccordion(collapseId) {
	$('#'+collapseId).collapse('toggle');
}

function stopProp(event) {
	event.stopPropagation();
}

function settings() {
	window.location.href = "./settings_tl.html";
};

function logout() {
	window.location.href = "./login.html";
};
</script>
</body>
</html>