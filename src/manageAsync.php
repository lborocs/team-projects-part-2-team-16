<!-- This page works on behalf of the manage.php page by recieving post data via an AJAX POST request -->
<?php
session_start();
$ErrorMessage = "";
$saved = "none";
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Fetch task information
$sqlInfo = "SELECT * FROM tasks ORDER BY progress ASC";
$resultInfo = mysqli_query($conn, $sqlInfo);

// Lightmode
if ($_SESSION["lightmode"] == 1) {
    $colour = "text-light bg-dark";
}
if ($_SESSION["lightmode"] != 1) {
    $colour = "bg-white";
}
//check user options request
if (!empty($_POST['deleteUser'])) {
    //deletes user
    $sqlDelete = "DELETE FROM users WHERE user_ID =" . $_POST['deleteUser'];
    mysqli_query($conn, $sqlDelete);
    //deletes any tasks the user had
    $sqlDelete = "DELETE FROM tasks WHERE user_ID =" . $_POST['deleteUser'];
    mysqli_query($conn, $sqlDelete);
} else if (!empty($_POST['setEmployee'])) {
    //set the user to an employee
    $sqlDelete = "UPDATE users SET role = 'Employee' WHERE user_ID =" . $_POST['setEmployee'];
    mysqli_query($conn, $sqlDelete);
} else if (!empty($_POST['setTL'])) {
    //set user to team leader
    $sqlDelete = "UPDATE users SET role = 'TL' WHERE user_ID =" . $_POST['setTL'];
    mysqli_query($conn, $sqlDelete);
} else if (!empty($_POST['setManager'])) {
    //set user to manager
    $sqlDelete = "UPDATE users SET role = 'Manager' WHERE user_ID =" . $_POST['setManager'];
    mysqli_query($conn, $sqlDelete);
}
// Sort By
if (!empty($_POST['sortbyName'])) {
    //get sort by request for surname alphabetically
    $sql = "SELECT user_ID, forename, surname, email, role, icon FROM users ORDER BY surname";
} elseif (!empty($_POST['sortbyCountA'])) {
    //get sort by request for count ascending
    $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', COUNT(t.user_ID) as 'count' 
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY COUNT(t.user_ID) ASC";
} elseif (!empty($_POST['sortbyCountD'])) {
    //get sort by request for count descending
    $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', COUNT(t.user_ID) as 'count' 
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY COUNT(t.user_ID) DESC";
} elseif (!empty($_POST['sortbyHoursA'])) {
    //get sort by request for hours ascending
    $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', SUM(t.est_hours) as 'total_est_hours'
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY SUM(t.est_hours) ASC";
} elseif (!empty($_POST['sortbyHoursD'])) {
    //get sort by request for hours descending
    $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', SUM(t.est_hours) as 'total_est_hours'
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY SUM(t.est_hours) DESC";
}

// apply filters via sql request

$result = mysqli_query($conn, $sql);

// display what's returned

foreach ($result as $user) {
    $count = $user['user_ID'];
    if ($user['role'] == 'TL') {
        $user['role'] = "Team Leader";
    }//formats role form tl to team leader
    if (!empty($_POST['filterManager'])) {
        //skips any roles which arent manager if filter is activated
        if ($user['role'] != "Manager") {
            continue;
        }
    } elseif (!empty($_POST['filterTL'])) {
        //skips any roles which arent team leader if filter is activated
        if ($user['role'] != "Team Leader") {
            continue;
        }
    } elseif (!empty($_POST['filterEmployee'])) {
        //skips any roles which arent employee if filter is activated
        if ($user['role'] != "Employee") {
            continue;
        }
    }
    if (!empty($_POST['search'])) {
        //applies the search filter to display users with a matching name
        $pattern = '/.*' . preg_quote($_POST['search'], '/') . '.*/i';
        if (!preg_match($pattern, ($user['forename'] . ' ' . $user['surname']))) {
            $hasTask = false;
            foreach ($resultInfo as $info) {
                if ($user['user_ID'] == $info['user_ID']) {
                    if (preg_match($pattern, $info['title']) || preg_match($pattern, $info['title'])) {
                        $hasTask = true;
                    }
                }
            }
            if (!$hasTask) {
                continue;
            }
        }
    }
    //if current user hsnt been skipped then display their accordian with respective data, same as manage.php
    echo '
	<div class="container accordion-item ' . $colour . ' text-dark" style="border-radius:5px;">
		<div class="row employee">
			<div class="col-md-4">
				<h5>' . $user['forename'] . ' ' . $user['surname'] . '</h5>
				<span class="badge rounded-pill bg-primary" style="font-size:1rem;">' . $user['role'] . '</span>
				<span class="badge rounded-pill bg-secondary" style="font-size:1rem;">' . $user['email'] . '</span>
			</div>
			<div class="col-md-3 $colour">
				<h6>Projects</h6>
				';
    mysqli_data_seek($resultInfo, 0);

    $printedProjects = [];
    $taskCount = 0;
    $hours = 0;
    foreach ($resultInfo as $info) {
        if ($user['user_ID'] == $info['user_ID']) {
            $taskCount = $taskCount + 1;
            $hours = $hours + $info['est_hours'];
            if (!in_array($info['project_ID'], $printedProjects)) {
                echo '<p style="margin-bottom: 2px;"><small>Project ' . $info['project_ID'] . '</small></p>';

                // Add the project to the printedProjects array
                $printedProjects[] = $info['project_ID'];
            }
        }
    }

    echo ' </div>
			<div class="col-md-3">
				<h6>Other Information</h6>
				<p class="taskcount" style="margin-bottom: 0px;">Tasks: ' . $taskCount . '</p>
				<p style="margin-bottom: 0px;">Task(s) Man Hours: ' . $hours . '</p>
			</div>
			<div class="col-md-2">
				<div class="btn-group">
					<button type="button" class="btn btn-primary disabled rights">Change Role</button>
					<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
						data-bs-toggle="dropdown" aria-expanded="false">
						<span class="visually-hidden">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu ' . $colour . '">
						<li><a class="dropdown-item ' . $colour . '" onclick="onSelect(\'' . $count . '\',\'mgr\')">Set Manager</a></li>
						<li><a class="dropdown-item ' . $colour . '" onclick="onSelect(\'' . $count . '\',\'tl\')">Set Team Leader</a></li>
						<li><a class="dropdown-item ' . $colour . '" onclick="onSelect(\'' . $count . '\',\'emp\')">Set Employee</a></li>
						<li>
							<hr class="dropdown-divider">
						</li>
						<li><a class="dropdown-item" href="#" style="color:red;" data-bs-toggle="modal" data-bs-target="#exampleModalDefault" onclick="onSelect(\'' . $count . '\',\'delete\')">
							Delete User
						</a></li>
					</ul>
				</div>
			</div>    
		</div>
		<h2 class="accordion-header ' . $colour . '"';
    if ($_SESSION["lightmode"] != 1) {
        echo "style = 'background-color:white;'";
    }
    echo '">
			<div class="accordion-button ' . $colour . '"';
    if ($_SESSION["lightmode"] != 1) {
        echo "style = 'background-color:white;'";
    }
    echo ' onclick="toggleAccordion(\'ID' . $count . '\')">
				<p class="col-md-12" style="text-align: center; padding-top: 5px;">View Assigned Tasks</p>
			</div>
		</h2>
		<div id="ID' . $count . '" class="accordion-collapse collapse ' . $colour . ' text-dark">
			<div class="accordion-body pt-0">
				<div class="container-fluid px-0 " style = "margin:3px 0px 0px 0px;">
					<div class="row flex-md-row horizontal-scroll flex-md-nowrap">';
    mysqli_data_seek($resultInfo, 0);
    $slider = [];
    foreach ($resultInfo as $info) {
        if ($user['user_ID'] == $info['user_ID']) {
            $taskCount = $taskCount + 1;
            if (!in_array($info['task_ID'], $slider)) {
                echo '
								<div class="col-12 col-md-3 mb-3 mb-md-0 ' . $colour . '">
									<div class="card card-body h-100 taskcard ' . $colour . '';
                if ($info['progress'] == '1') {
                    echo ' task-in-progress';
                }
                if ($info['progress'] == '0') {
                    echo ' task-incomplete';
                }
                if ($info['progress'] == '2') {
                    echo ' task-completed';
                }
                echo '">
										<h5 class="card-title">' . $info['title'] . '</h5>
										<p class="card-text mb-0">' . $info['description'] . '</p>
										<p class="card-text mb-0 mt-auto"><small class="text-muted">Task length: ' . $info['est_hours'] . '
												hours</small></p>
										<p class="card-text"><small class="text-muted">Due: ' . $info['due_date'] . '</small></p>
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
    // this javascript is used to create arrays of variables to store the current filter values, so that when a second, or even third filter is applied simultaneously,
    //none of the previosuly activated filters are lost.
}
echo "<script>var sorting = [" . !empty($_POST['sortbyHoursD']) . "," . !empty($_POST['sortbyHoursA']) . "," . !empty($_POST['sortbyCountD']) . "," . !empty($_POST['sortbyCountA']) . "," . !empty($_POST['sortbyName']) . "]; 
				var filters = [" . !empty($_POST['filterManager']) . "," . !empty($_POST['filterTL']) . "," . !empty($_POST['filterEmployee']) . "];
				var search = ['";
if (!empty($_POST['search'])) {
    echo $_POST['search'];
} else {
    echo !empty($_POST['search']);
}
echo "'];</script>";
?>
