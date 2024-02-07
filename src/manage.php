<?php 
	session_start();
	$ErrorMessage = "";
	$saved = "none";
	include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
	//Lightmode
	if($_SESSION["lightmode"] == 1){
		$colour = "text-light bg-dark";
	}if($_SESSION["lightmode"] != 1){
		$colour = "bg-white";
	}
	
	//Tasks
	$sql = "SELECT *
			FROM tasks
			ORDER BY progress ASC";
	$resultInfo = mysqli_query($conn,$sql);

	if (!$resultInfo) {
		echo "Connection Error.";
		exit;
	}
	$sql = "SELECT user_ID,forename,surname,email,role,icon
		FROM users ORDER BY surname";
	$result = mysqli_query($conn,$sql);

	if (!$result) {
		echo "Connection Error.";
		exit;
	}
?>
<html class = "<?php echo $colour;?>">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Employees</title>

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
<link href="./headers.css" rel="stylesheet">
<link href="./emp.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
	@media (min-width: 768px) {
		.bd-placeholder-img-lg {
			font-size: 3.5rem;
		}
	}

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

	#page-content {
		margin-bottom: 12rem;
	}

	#page-header {
		padding: 2rem 1rem;
		margin-bottom: 1rem;
	}

	.accordion-button:not(.collapsed) {
		background-color: #FFFFFF;
		color: #212529;
		box-shadow: none;
		border:none;
		cursor: pointer;
	}

	.accordion-button:focus {
		box-shadow: none;
		border:none;
		background-color: #FFFFFF;
		cursor: pointer;
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
</head>

<body class = "<?php echo $colour;?>">
	<div style="margin:0px; padding:0px;"  class = "<?php echo $colour;?>">
			<?php 
				if(!isset($_SESSION["role"])){
					echo "<script>window.location.href='./login.php'</script>";
				}else if($_SESSION["role"] == "Manager"){
					$taskcreate = "link-dark";
					$topicview = "link-dark";
					$dashview = "link-dark";
					include "./navbar_m.php";
				}else if($_SESSION["role"] == "TL"){
					echo "<script>window.location.href='./login.php'</script>";
				}else if($_SESSION["role"] == "Employee"){
					echo "<script>window.location.href='./login.php'</script>";
				}
			?>
	</div>
	<div class="<?php if($_SESSION["lightmode"] == 1){echo 'bg-secondary text-light border-light border-bottom';}else{echo 'bg-dark text-light';}?>  px-4 py-5 text-center" style="margin:0px; padding:0px;">
		<div class="py-5">
			<h1 class="display-5 fw-bold ">Manage Employees</h1>
		</div>
	</div>
	<div class="container <?php echo $colour;?>" style="margin-bottom: 10px;">
		<div id="page-header">
	<div class = "row">
		<div class="dropdown align-items-right col-md-1">
			<button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdownMenuButton"
				data-bs-toggle="dropdown">Sort By</button>
			<div class=" dropdown-menu <?php echo $colour;?>" aria-labelledby="sortDropdownMenuButton">
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="sortData('sortbyCountD')">Total Task Count (Desc)</button>
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="sortData('sortbyCountA')">Total Task Count (Asc)</button>
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="sortData('sortbyHoursD')">Total Task Hours (Desc)</button>
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="sortData('sortbyHoursA')">Total Task Hours (Asc)</button>
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="sortData('sortbyName')">By Surname</button>
			</div>
		</div>

		<div class="dropdown align-items-right col-md-1">
			<button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
				data-bs-toggle="dropdown">Filter For</button>
			<div class=" dropdown-menu <?php echo $colour;?>" aria-labelledby="filterDropdownMenuButton">
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="filterData('filterManager')">Manager</button>
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="filterData('filterTL')">Team Leader</button>
				<button class="dropdown-item <?php echo $colour;?>" type="button" onclick="filterData('filterEmployee')">Employee</button>
				<button class="dropdown-item <?php echo $colour;?>" style = "color:red;" type="button" onclick="filterData('null')">Cancel</button>

			</div>
		</div>

	<script>
		function sortData(sortOption) {
			fetchData(sortOption, null);
		}

		function filterData(filterOption) {
			fetchData(null, filterOption);
		}

		function fetchData(sortOption, filterOption) {
			var data = {};
			// Check which sorting option is selected and add it to the data object
			if (sortOption === 'sortbyName') {
				data.sortbyName = sortOption;
			} else if (sortOption === 'sortbyCountA') {
				data.sortbyCountA = sortOption;
			} else if (sortOption === 'sortbyCountD') {
				data.sortbyCountD = sortOption;
			} else if (sortOption === 'sortbyHoursA') {
				data.sortbyHoursA = sortOption;
			} else if (sortOption === 'sortbyHoursD') {
				data.sortbyHoursD = sortOption;
			}else{
				if(sorting[0] == 1){
					data.sortbyHoursD = 'sortbyHoursD';
				}else if(sorting[1] == 1){
					data.sortbyHoursA = 'sortbyHoursA';
				}else if(sorting[2] == 1){
					data.sortbyCountD = 'sortbyCountD';
				}else if(sorting[3] == 1){
					data.sortbyCountA = 'sortbyCountA';
				}else{
					data.sortbyName = 'sortbyName';
				}
			}

			// Check which filter option is selected and add it to the data object
			if (filterOption === 'filterManager') {
				data.filterManager = filterOption;
			} else if (filterOption === 'filterTL') {
				data.filterTL = filterOption;
			} else if (filterOption === 'filterEmployee') {
				data.filterEmployee = filterOption;
			}else if (filterOption == 'null'){
				//do nothing
			}else{
				if(filters[0] == 1){
					data.filterManager = 'filterManager';
				}else if(filters[1] == 1){
					data.filterTL = 'filterTL';
				}else if(filters[2] == 1){
					data.filterEmployee = 'filterEmployee';
				}
			}
		


			$.ajax({
				url: 'manageAsync.php',
				method: 'POST',
				data: data,
				success: function(response) {
					// Handle success
					$('#displayedContent').html(response); // Update displayed content
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error); // Log error to the console
				}
			});

		}
	</script>

				<div class="col-md-10 ">
					<input type="search" class="form-control" placeholder="Search Employees" aria-label="Search">
				</div>
				</div>
			</div>
		</div>
	</div>
	<div class="b-example-divider  <?php echo $colour;?>"></div>
<div id = "displayedContent">
<?php
$count = 0;

foreach ($result as $user){
	if ($user['role'] == 'TL'){
		$user['role'] = "Team Leader";
	}

	echo '
	<div class="container accordion-item '.$colour.' text-dark" style="border-radius:5px;">
		<div class="row employee">
			<div class="col-md-4">
				<h5>'.$user['forename'].' '.$user['surname'].'</h5>
				<span class="badge rounded-pill bg-primary" style="font-size:1rem;">'.$user['role'].'</span>
				<span class="badge rounded-pill bg-secondary" style="font-size:1rem;">'. $user['email'].'</span>
			</div>
			<div class="col-md-3 $colour">
				<h6>Projects</h6>
				';
				mysqli_data_seek($resultInfo, 0);

				$printedProjects = [];
				$taskCount = 0;
				$hours = 0; 
                foreach ($resultInfo as $info) {
                    if ($user['user_ID'] == $info['user_ID']){
						$taskCount = $taskCount+1;
						$hours = $hours + $info['est_hours'];
						 if(!in_array($info['project_ID'], $printedProjects)) {
							echo '<p style="margin-bottom: 2px;"><small>Project ' . $info['project_ID'] . '</small></p>';
							
							// Add the project to the printedProjects array
							$printedProjects[] = $info['project_ID'];
						}
					}
                }

			echo ' </div>
			<div class="col-md-3">
				<h6>Other Information</h6>
				<p class="taskcount" style="margin-bottom: 0px;">Tasks: '.$taskCount.'</p>
				<p style="margin-bottom: 0px;">Task(s) Man Hours: '.$hours.'</p>
			</div>
			<div class="col-md-2">
				<div class="btn-group">
					<button type="button" class="btn btn-primary disabled rights">Change Role</button>
					<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
						data-bs-toggle="dropdown" aria-expanded="false">
						<span class="visually-hidden">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu '.$colour.'">
						<li><a class="dropdown-item" >Set Manager</a></li>
						<li><a class="dropdown-item" >Set Employee</a></li>
						<li>
							<hr class="dropdown-divider '.$colour.'">
						</li>
						<li><a class="dropdown-item '.$colour.'" href="#" style="color:red;">Delete User</a></li>
					</ul>
				</div>
			</div>    
		</div>
		<h2 class="accordion-header '.$colour.'"';if($_SESSION["lightmode"] != 1){echo "style = 'background-color:white;'";} echo '">
			<div class="accordion-button '.$colour.'"';if($_SESSION["lightmode"] != 1){echo "style = 'background-color:white;'";} echo ' onclick="toggleAccordion(\'ID'.$count.'\')">
				<p class="col-md-12" style="text-align: center; padding-top: 5px;">View Assigned Tasks</p>
			</div>
		</h2>
		<div id="ID'.$count.'" class="accordion-collapse collapse '.$colour.' text-dark">
			<div class="accordion-body pt-0">
				<div class="container-fluid px-0 " style = "margin:3px 0px 0px 0px;">
					<div class="row flex-md-row horizontal-scroll flex-md-nowrap">';
				mysqli_data_seek($resultInfo, 0);
				$slider = [];
				foreach ($resultInfo as $info) {
                    if ($user['user_ID'] == $info['user_ID']){
						$taskCount = $taskCount+1;
						 if(!in_array($info['task_ID'], $slider)) {
							echo '
								<div class="col-12 col-md-3 mb-3 mb-md-0 '.$colour.'">
									<div class="card card-body h-100 taskcard '.$colour.''; 
										if($info['progress'] == '1'){echo ' task-in-progress';}
										if($info['progress'] == '0'){echo ' task-incomplete';}
										if($info['progress'] == '2'){echo ' task-completed';}
										echo'">
										<h5 class="card-title">'.$info['title'].'</h5>
										<p class="card-text mb-0">'.$info['description'].'</p>
										<p class="card-text mb-0 mt-auto"><small class="text-muted">Task length: '.$info['est_hours'].'
												hours</small></p>
										<p class="card-text"><small class="text-muted">Due: '.$info['due_date'].'</small></p>
									</div>
								</div>';
							$slider[] = $info['task_ID'];
						}
					}
                }
				echo '</div>
				</div>
			</div>
		</div>
	</div>

	<br>';
	$count = $count +1;
}echo "<script>var sorting = [".!empty($_POST['sortbyHoursD']).",".!empty($_POST['sortbyHoursA']).",".!empty($_POST['sortbyCountD']).",".!empty($_POST['sortbyCountA']).",".!empty($_POST['sortbyName'])."]; 
var filters = [".!empty($_POST['filterManager']).",".!empty($_POST['filterTL']).",".!empty($_POST['filterEmployee'])."];</script>";
?>
</div>
		<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top <?php echo $colour;?>"
			style="padding-left: 25px; padding-right: 25px;">
			<p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

			<a href="/"
				class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none <?php echo $colour;?>">
				<img src="./logo.png" alt="mdo" width="200" height="50">
				</svg>
			</a>

			<div class="justify-content-end <?php echo $colour;?>">
				<p>Phone: 01509 888999</p>
				<p>Email: king@make-it-all.co.uk</p>
			</div>
		</footer>

		<script>
			function toggleAccordion(collapseId) {
				$('#' + collapseId).collapse('toggle');
			}

			function stopProp(event) {
				event.stopPropagation();
			}

			function settings() {
				window.location.href = "./settings_tl.html";
			}

			function logout() {
				window.location.href = "./login.html";
			}
		</script>

</body>

</html>