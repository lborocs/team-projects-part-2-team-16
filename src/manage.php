<!-- This page works asynchronously alongaside the manageAsync.php page to provide managers with a view of all employees,
as well as each employees role, their tasks set, the projects which they are a part of,
total task hours and also allows the manager to chage role of and delete each user. 

There are also filters on this page, to allow for different methods of sorting and filtering. Inlucidng filtering by role, and by name (via search bar)-->
<?php
session_start();
$ErrorMessage = "";
$userDeleted = "none";
include "db_connection.php";
$conn = mysqli_connect($servername, $username, $password, $dbname);
//lightmode colour
if ($_SESSION["lightmode"] == 1) {
    $colour = "text-light bg-dark";
}
if ($_SESSION["lightmode"] != 1) {
    $colour = "bg-white";
}

//select data from the tasks and orders them by progress
$sql = "SELECT *
			FROM tasks
			ORDER BY progress ASC";
$resultInfo = mysqli_query($conn, $sql);

if (!$resultInfo) {
    echo "Connection Error.";
    exit;
}
//selects data to display on all users
$sql = "SELECT user_ID,forename,surname,email,role,icon
		FROM users ORDER BY surname";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Connection Error.";
    exit;
}
?>
<html class="<?php echo $colour; ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Employees</title>
    <link rel="icon" type="image/x-icon" href="./logo.ico">

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
            padding: 2rem 0rem;
            margin-bottom: 1rem;
        }

        .accordion-button:not(.collapsed) {
            background-color: #FFFFFF;
            color: #212529;
            box-shadow: none;
            border: none;
            cursor: pointer;
        }

        .accordion-button:focus {
            box-shadow: none;
            border: none;
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

<body class="<?php echo $colour; ?>">
    <!-- the following adds the manager navbar to the top of the screen, and if anyone other than a manager attempts to access this page
    then they are logged out and returned to the login page. -->
    <div style="margin:0px; padding:0px;" class="<?php echo $colour; ?>">
        <?php
        if (!isset($_SESSION["role"])) {
            echo "<script>window.location.href='./login.php'</script>";
        } else if ($_SESSION["role"] == "Manager") {
            $taskcreate = "link-dark";
            $topicview = "link-dark";
            $dashview = "link-dark";
            include "./navbar_m.php";
        } else if ($_SESSION["role"] == "TL") {
            echo "<script>window.location.href='./login.php'</script>";
        } else if ($_SESSION["role"] == "Employee") {
            echo "<script>window.location.href='./login.php'</script>";
        }
        ?>
    </div>
    <div class="<?php if ($_SESSION["lightmode"] == 1) {
        echo 'bg-secondary text-light border-light border-bottom';
    } else {
        echo 'bg-dark text-light';
    } ?>  px-4 py-5 text-center"
        style="margin:0px; padding:0px;">
        <div class="py-5">
            <h1 class="display-5 fw-bold ">Manage Employees</h1>
        </div>
    </div>
    <div class="container <?php echo $colour; ?>" style="margin-bottom: 10px;">
        <div id="page-header">
            <div class="row">
                <div class="dropdown col-sm-2 col-6" style="padding: 0px">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdownMenuButton"
                        data-bs-toggle="dropdown" style="width: 90%;">Sort By</button>
                    <!-- The following set is used to activate a sort by filter -->
                    <div class=" dropdown-menu <?php echo $colour; ?>" aria-labelledby="sortDropdownMenuButton">
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="sortData('sortbyCountD')">Total Task Count (Desc)</button>
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="sortData('sortbyCountA')">Total Task Count (Asc)</button>
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="sortData('sortbyHoursD')">Total Task Hours (Desc)</button>
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="sortData('sortbyHoursA')">Total Task Hours (Asc)</button>
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="sortData('sortbyName')">By Surname</button>
                    </div>
                </div>

                <div class="dropdown col-sm-2 col-6" style="padding: 0px">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                        data-bs-toggle="dropdown" style="width: 90%;">Filter For</button>
                    <!-- The following set is used to activate a filter by role. Also removes filter -->
                    <div class=" dropdown-menu <?php echo $colour; ?>" aria-labelledby="filterDropdownMenuButton">
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="filterData('filterManager')">Manager</button>
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="filterData('filterTL')">Team Leader</button>
                        <button class="dropdown-item <?php echo $colour; ?>" type="button"
                            onclick="filterData('filterEmployee')">Employee</button>
                        <button class="dropdown-item <?php echo $colour; ?>" style="color:red;" type="button"
                            onclick="filterData('null')">Cancel</button>

                    </div>
                </div>
                <div class="col-sm-8 col-12" style="padding: 0px">
                    <!-- The following set is used to search for an employee by name -->
                    <input id='searchbar' type="search" class="form-control" placeholder="Search Employees or Tasks"
                        oninput="searchData()" aria-label="Search">
                </div>

                <script>
                    function searchData() {
                        //activated via typing in the search bar updated asynchronously
                        var data = {};
                        //used to keep fiters active
                        if (filters[0] == 1) {
                            data.filterManager = 'filterManager';
                        } else if (filters[1] == 1) {
                            data.filterTL = 'filterTL';
                        } else if (filters[2] == 1) {
                            data.filterEmployee = 'filterEmployee';
                        }
                        //used to keep sorting acting
                        data.sortbyName = 'sortbyName';
                        if (sorting[0] == 1) {
                            data.sortbyHoursD = 'sortbyHoursD';
                        } else if (sorting[1] == 1) {
                            data.sortbyHoursA = 'sortbyHoursA';
                        } else if (sorting[2] == 1) {
                            data.sortbyCountD = 'sortbyCountD';
                        } else if (sorting[3] == 1) {
                            data.sortbyCountA = 'sortbyCountA';
                        }
                        //checks the value isnt blank
                        if (document.getElementById('searchbar').value != '') {
                            data.search = document.getElementById('searchbar').value;
                        }

                        $.ajax({
                            url: 'manageAsync.php',
                            method: 'POST',
                            data: data,
                            success: function (response) {
                                $('#displayedContent').html(response); // change displayed content
                            },
                            error: function (error) {
                                console.error(error); // log error to the console
                            }
                        });

                    }

                    function sortData(sortOption) {
                        // activates the sorting version of fetchdata
                        fetchData(sortOption, null);
                    }

                    function filterData(filterOption) {
                        // activates the filter version of fetchdata
                        fetchData(null, filterOption);
                    }

                    function fetchData(sortOption, filterOption) {
                        var data = {};
                        //if both are null we want to delete a user and refresh
                        if ((sortOption == null) && (filterOption == null)) {
                            if (optionsID[0] == "D") {
                                data.deleteUser = optionsID.slice(1);
                            } else if (optionsID[0] == "E") {
                                data.setEmployee = optionsID.slice(1);
                            } else if (optionsID[0] == "T") {
                                data.setTL = optionsID.slice(1);
                            } else if (optionsID[0] == "M") {
                                data.setManager = optionsID.slice(1);
                            }
                        }
                        // check which sorting option is selected and add it to the data object
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
                        } else {
                            if (sorting[0] == 1) {
                                data.sortbyHoursD = 'sortbyHoursD';
                            } else if (sorting[1] == 1) {
                                data.sortbyHoursA = 'sortbyHoursA';
                            } else if (sorting[2] == 1) {
                                data.sortbyCountD = 'sortbyCountD';
                            } else if (sorting[3] == 1) {
                                data.sortbyCountA = 'sortbyCountA';
                            } else {
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
                        } else if (filterOption == 'null') {
                            //do nothing
                        } else {
                            if (filters[0] == 1) {
                                data.filterManager = 'filterManager';
                            } else if (filters[1] == 1) {
                                data.filterTL = 'filterTL';
                            } else if (filters[2] == 1) {
                                data.filterEmployee = 'filterEmployee';
                            }
                        }


                        //search preservation

                        if ((search[0] != 0) && (search[0] != '') && (typeof search[0] != 'undefined')) {
                            data.search = search[0];
                        }

                        //passes data onto manageAsync
                        $.ajax({
                            url: 'manageAsync.php',
                            method: 'POST',
                            data: data,
                            success: function (response) {
                                // handle success
                                $('#displayedContent').html(response); // update displayed content
                            },
                            error: function (xhr, status, error) {
                                // Handle error
                                console.error(error); // log error to the console
                            }
                        });

                    }
                    var optionsID;
                    //create a global optionsID variable so that it can be passed to manageAsync. This is the variable in which has been selected for a role change,
                    //or has been selected to be deleted
                    function onSelect(selected_id, selection) {
                        if (selected_id == null) {
                            //hides the confirmation message when the user presses the cross
                            if (selection == "emp") {
                                document.getElementById('empNotification').style.display = "none";
                            } else if (selection == "tl") {
                                document.getElementById('tlNotification').style.display = "none";
                            } else if (selection == "mgr") {
                                document.getElementById('mgrNotification').style.display = "none";
                            } else if (selection == "del") {
                                document.getElementById('deletedNotification').style.display = "none";
                            }
                        } else if (selection == "delete") {
                            //sets the id to delete mode
                            optionsID = "D" + selected_id;
                        } else {
                            //sets the id to change permissions mode
                            if (selection == "emp") {
                                optionsID = "E" + selected_id;
                                document.getElementById('empNotification').style.display = "block";
                            } else if (selection == "tl") {
                                optionsID = "T" + selected_id;
                                document.getElementById('tlNotification').style.display = "block";
                            } else if (selection == "mgr") {
                                optionsID = "M" + selected_id;
                                document.getElementById('mgrNotification').style.display = "block";
                            }
                            //calls the change permissions/delete version of fetchData
                            fetchData(null, null)
                        }
                    }
                    function deleteConfimred() {
                        //shows message to confirm when a deletion has successfully occured
                        document.getElementById('deletedNotification').style.display = "block";
                        fetchData(null, null)
                    }
                </script>


            </div>
        </div>
    </div>
    </div>
    <div class="b-example-divider  <?php echo $colour; ?>"></div>
    <!-- A series of all deletion/role change confirmation messages -->
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="deletedNotification"
        style="display:none;">
        User Deleted.
        <button type="button" class="btn-close" aria-label="Close" onclick="onSelect(null,'del')"></button>
    </div>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="empNotification"
        style="display:none;">
        User changed to Employee role.
        <button type="button" class="btn-close" aria-label="Close" onclick="onSelect(null,'emp')"></button>
    </div>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="tlNotification" style="display:none;">
        User changed to Team Leader role.
        <button type="button" class="btn-close" aria-label="Close" onclick="onSelect(null,'tl')"></button>
    </div>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="mgrNotification"
        style="display:none;">
        User changed to Manager role.
        <button type="button" class="btn-close" aria-label="Close" onclick="onSelect(null,'mgr')"></button>
    </div>
    <div id="displayedContent">
        <?php

        //loop throuhg each requested user to display all details for them
        foreach ($result as $user) {
            //changes role from TL to team leader for formatting
            if ($user['role'] == 'TL') {
                $user['role'] = "Team Leader";
            }
            $count = $user['user_ID']; //sets the count to the current user id so as to know which user has been selected for deletion etc
            //starts to echo an accordion and formatted display of each users data. This take palce for every user in the reuest 
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
            //loop through and display tasks, along with colour code within a slider
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
    </div>
    <!-- Modal used to confirm deletion of user -->
    <div class="modal fade" id="exampleModalDefault" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" stule="padding: 0px 2px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Delete User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                        onclick="deleteConfimred()">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <footer
        class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top <?php echo $colour; ?>"
        style="padding-left: 25px; padding-right: 25px;">
        <p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

        <a href="/"
            class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none <?php echo $colour; ?>">
            <img src="./logo.png" alt="mdo" width="200" height="50">
            </svg>
        </a>

        <div class="justify-content-end <?php echo $colour; ?>">
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
