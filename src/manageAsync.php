<?php	
    session_start();
    $ErrorMessage = "";
    $saved = "none";
    include "db_connection.php";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Fetch task information
    $sqlInfo = "SELECT * FROM tasks";
    $resultInfo = mysqli_query($conn, $sqlInfo);

    // Lightmode
	if($_SESSION["lightmode"] == 1){
		$colour = "text-light bg-dark";
	}if($_SESSION["lightmode"] != 1){
		$colour = "bg-white";
	}

    // Sort By
    if (!empty($_POST['sortbyName'])) {
        $sql = "SELECT user_ID, forename, surname, email, role, icon FROM users ORDER BY surname";
    } elseif (!empty($_POST['sortbyCountA'])) {
        $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', COUNT(t.user_ID) as 'count' 
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY COUNT(t.user_ID) ASC";
    } elseif (!empty($_POST['sortbyCountD'])) {
        $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', COUNT(t.user_ID) as 'count' 
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY COUNT(t.user_ID) DESC";
    } elseif (!empty($_POST['sortbyHoursA'])) {
        $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', SUM(t.est_hours) as 'total_est_hours'
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY SUM(t.est_hours) ASC";
    } elseif (!empty($_POST['sortbyHoursD'])) {
        $sql = "SELECT u.user_ID as 'user_ID', u.forename as 'forename', u.surname as 'surname', u.email as 'email', u.role as 'role', SUM(t.est_hours) as 'total_est_hours'
                FROM users u
                LEFT JOIN tasks t ON t.user_ID = u.user_ID
                GROUP BY u.user_ID 
                ORDER BY SUM(t.est_hours) DESC";
    }

    // Apply Filters

    $result = mysqli_query($conn, $sql);

    // Display Results
    $count = 0;
    
    foreach ($result as $user){
        if ($user['role'] == 'TL'){
            $user['role'] = "Team Leader";
        }
        if (!empty($_POST['filterManager'])) {
            if($user['role'] != "Manager"){
                continue;
            }
        } elseif (!empty($_POST['filterTL'])) {
            if($user['role'] != "Team Leader"){
                continue;
            }
        } elseif (!empty($_POST['filterEmployee'])) {
            if($user['role'] != "Employee"){
                continue;
            }
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
