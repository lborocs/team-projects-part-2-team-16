<?php
    session_start();
    include "db_connection.php";
    include "add_task.php";
    
    // this function now in seperate file

    // function create_task() {
    //     if (isset($_POST["employee"])){
    //         $empID = $_POST["employee"];
    //         if (!is_numeric($empID)) {
    //             return false;
    //         } else {
    //             $empID = intval($empID);
    //         }
    //     } else {
    //         return false;
    //     }
    //     if (isset($_POST["project"])) {
    //         $projectID = $_POST["project"];
    //         if (!is_numeric($projectID)) {
    //             return false;
    //         } else {
    //             $projectID = intval($projectID);
    //         }
    //     } else {
    //         return false;
    //     }
    //     if (isset($_POST["title"])){
    //         $title = $_POST["title"];
    //         if (strlen($title) > 255) {
    //             return false;
    //         }
    //     } else {
    //         return false;
    //     }
    //     if (isset($_POST["description"])) {
    //         $description = $_POST["description"];
    //         if (strlen($description) > 1000) {
    //             return false;
    //         }
    //     } else {
    //         return false;
    //     }
    //     if (isset($_POST["date"])) {
    //         $date = $_POST["date"];
    //         if (!date_create_from_format("Y-m-d", $date)) {
    //             return false;
    //         }
    //     } else {
    //         return false; 
    //     }
    //     if (isset($_POST["manhours"])) {
    //         $hours = $_POST["manhours"];
    //         if (!is_numeric($hours)) {
    //             return false;
    //         }else {
    //             $hours = intval($hours);
    //         }
    //     } else {
    //         return false;
    //     }
        
        
    //     // $conn = mysqli_connect($servername, $username, $password, $dbname);
    //     // if (!$conn) {
    //     //     echo "Connection Error.";
    //     //     exit;
    //     // }
    //     try {
    //         include "db_connection.php";

    //         $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
    //     } catch (PDOException $e) {
    //         echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
    //         return false;
    //     }

    //     $result = $conn->query("SELECT max(task_ID) FROM tasks");
    //     $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
    //     if ($maxID == null) {
    //         $ID = 1;
    //     } else {
    //         $ID = $maxID[0] + 1;
    //     }
        
    //     // $stmt = $conn->prepare("insert into tasks (task_ID, user_ID, project_ID, title, description, due_date, est_hours)
    //     //                          values ($ID, $empID, $projectID, '$title', '$description', DATE '$date', $hours);");
    //     $create_task_stmt = $conn->prepare("INSERT into tasks (task_ID, user_ID, project_ID, title, description, due_date, est_hours, progress) 
    //                                     VALUES (:ID, :empID, :projectID, :title, :description, DATE :date, :hours, 0)");
    //     // $stmt = $conn->prepare("INSERT into tasks (task_ID, user_ID, project_ID, title, description, due_date, est_hours, progress) VALUES (10, 2, 3, 'adfsgdf', 'dsfgdfg', DATE '3000-12-12', 1, 0)");
    //     $create_task_stmt->bindParam(':ID', $ID, PDO::PARAM_INT);
    //     $create_task_stmt->bindParam(':empID', $empID, PDO::PARAM_INT);
    //     $create_task_stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
    //     $create_task_stmt->bindParam(':title', $title, PDO::PARAM_STR);
    //     $create_task_stmt->bindParam(':description', $description, PDO::PARAM_STR);
    //     $create_task_stmt->bindParam(':date', $date, PDO::PARAM_STR);
    //     $create_task_stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
        
    //     if ($create_task_stmt->execute())  {
    //         header("location: dashboard.php");
    //         die();
    //     } else {
    //         echo "<script type='text/javascript'>alert('request unsucesfull');</script>";
    //     }
    // }

    // gets the task to be edited from the DB
    function get_edit_task() {
        try {
            include "db_connection.php";
            $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
        } catch (PDOException $e) {
            echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
            return false;
        }
    
        $getTaskQuery = $conn->prepare("SELECT * FROM tasks where task_ID = :task_ID");
        $getTaskQuery->bindParam(":task_ID", $_GET["edit_ID"], PDO::PARAM_INT);
        if(!$getTaskQuery->execute()) {
            header("location:  ");
        }
        return $getTaskQuery->fetch(PDO::FETCH_ASSOC);
    }

    // validates and then commits the changes made to an already existing task
    function editTask() {
        try {
            include "db_connection.php";
            $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
        } catch (PDOException $e) {
            echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
            return false;
        }

        // validation functions defined in add_task.php 
        if (!isset($_POST["edit_ID"])) {
            return; 
        } else if (!is_numeric($_POST["edit_ID"])) {
            return;
        }
        $task_ID = intval($_POST["edit_ID"]);

        $check = validate_title_desc();
        if (!$check[0]) {
            echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
            return;
        }
        $title = $_POST["title"];
        $description = $_POST["description"];

        $check = validate_date_time();
        if (!$check[0]) {
            echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
            return;
        }
        $date = $_POST["date"];
        $hours = intval($_POST["manhours"]);

        $check = validate_user();
        if (!$check[0]) {
            echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
            return;
        }
        $empID = intval($_POST["employee"]);

        $edit_task_stmt = $conn->prepare("UPDATE tasks set title = :title, description = :description, due_date = Date :due_date, est_hours = :est_hour, user_ID = :user_ID where task_ID = :task_ID");
        $edit_task_stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $edit_task_stmt->bindParam(":description", $description, PDO::PARAM_STR);
        $edit_task_stmt->bindParam(":due_date", $date, PDO::PARAM_STR);
        $edit_task_stmt->bindParam(":est_hour", $hours, PDO::PARAM_INT);
        $edit_task_stmt->bindParam(":user_ID", $empID, PDO::PARAM_INT);
        $edit_task_stmt->bindParam(":task_ID", $task_ID, PDO::PARAM_INT);

        if ($edit_task_stmt->execute())  {
            header("location: dashboard.php");
            die();
        } else {
            echo "<script>alert('request unsucessful');</script>";
        }
    }

    // validates permissions and then deletes a task
    function deleteTask() {
        try {
            include "db_connection.php";
            $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
        } catch (PDOException $e) {
            echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
            return false;
        }

        if (!isset($_POST["edit_ID"])) {
            return; 
        } else if (!is_numeric($_POST["edit_ID"])) {
            return;
        }
        $task_ID = $_POST["edit_ID"];
        if ($_SESSION["role"] == "Manager") {
            if($conn->query("delete from tasks where task_ID = $task_ID") === false) {
                echo "<script type='text/javascript'>alert('failed to delete the task, an unexpected error occured');</script>";
            }
        // if the user attempting to delete the task is a team leader they have to be the leader of the projet the task is assigned to 
        } else if ($_SESSION["role"] == "TL") {
            $project_query = $conn->query("select project_ID from tasks where task_ID = $task_ID");
            $result = $project_query->fetch(PDO::FETCH_ASSOC)["project_ID"];
            $check = validate_project($result, $conn);
            if (!$check[0]) {
                echo "<script>alert('Failed to delete the task. One of your input violated input requirments: ".$check[1]."');</script>";
                return;
            }
            if($conn->query("delete from tasks where task_ID = $task_ID") === false) {
                echo "<script type='text/javascript'>alert('failed to delete the task, an unexpected error occured');</script>";
                header("location: dashboard.php");
            } else {
                header("location: dashboard.php");
                die();
            }
        }
    }
    
    
    if (isset($_POST["submitButton"])) {
        try {
            $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
            create_task(null, $conn);
            header("location: dashboard.php");
		    die();
        } catch (PDOException $e) {
            echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
            return false;
        }
    } else if (isset($_POST["confirmEditButton"])) {
        editTask();
    } else if (isset($_POST["deleteButton"])) {
        deleteTask();
    } else if (isset($_GET["edit_ID"])) {
        $editingTask = get_edit_task();
        $editTaskTitle = $editingTask["title"];
        $editTaskDesc = $editingTask["description"];
        $editTaskDate = $editingTask["due_date"];
        $editTaskHours = $editingTask["est_hours"];
        $editTaskUser = $editingTask["user_ID"];
    } 
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="./logo.ico">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Task</title>

    <!-- <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/"> -->

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->

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
    </style>

    <link rel="stylesheet" href="./searchable_dropdown.css">
</head>

<!-- adds dark mode classes to all elements if dark mode enabled -->
<script>
    <?php
    if($_SESSION["lightmode"] == 1){
		$colour = "text-light bg-dark";
	}else{
		$colour = "";
	}
    ?>
    
    $(document).ready(function() {
        if ("<?php echo $colour ?>" == "text-light bg-dark") {
            $("*").each(function() {
                if ($(this).hasClass("no-dark") == false) {
                    $(this).addClass("text-light bg-dark");
                }
            });
        }
    })
</script>

<body>
    <?php
    // loads the header if the user has the right perms
    if (!isset($_SESSION["role"])) {
        echo "<script>window.location.href='./login.php'</script>";
    } else if ($_SESSION["role"] == "Manager") {
        $taskcreate = "border-bottom border-primary link-primary";
        $topicview = "link-dark";
        $dashview = "link-dark";
        include "./navbar_m.php";
    } else if ($_SESSION["role"] == "TL") {
        $topicview = "link-dark";
        $taskcreate = "border-bottom border-primary link-primary";
        $taskview = "link-dark";
        $dashview = "link-dark";
        include "./navbar_tl.php";
    } else {
        header("location: dashboard.php");
    }
    ?>

    <main class="container" style="margin:auto; flex: 70%;">
        <h1 class="my-5"><?php if (isset($editingTask)) {echo "Edit";} else {echo "Assign";}?> Task</h1>
        <form autocomplete="off" method="post" action="">

            <!-- title input -->
            <div class="form-group row">
                <label for="title" class="col-auto-2 col-form-label" style="margin-left: 0px; margin-right: 0px;">Task Name</label>
                <div>
                    <input type="title" name="title" class="form-control" id="title" placeholder="..." maxlength="255" required value="<?php if (isset($editingTask)) {echo $editTaskTitle;}?>">
                </div>
            </div>

            <!-- description  input -->
            <div class="form-group row" style="margin-left: 0px; margin-right: 0px;">
                <label for="description" style="padding-left: 0px;">Task Description</label>
                <textarea class="form-control" id="description" name="description" rows="10" placeholder="..." maxlength="1000" required><?php if (isset($editingTask)) {echo $editTaskDesc;}?></textarea>
            </div>

            <!-- task length in hours and due date input -->
            <div style="display: flex;">
                <div style="flex-direction: row;">
                    <label for="manhours">Estimated Man Hours</label>
                    <input type="number" id="manhours" name="manhours" class="form-control" placeholder="Hours" style="width: 250px;" min="1" required value=<?php if (isset($editingTask)) {echo $editTaskHours;}?>>
                </div>
                <div style="flex-direction: row; margin-left: 50px;">
                    <label for="date">Select Due Date</label>
                    <input class="form-control" id="date" name="date" type="date" style="width: 250px;" required value="<?php if (isset($editingTask)) {echo $editTaskDate;}?>">
                    <script>    
                        let date = new Date(); 
                        document.getElementById("date").min = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
                    </script>
                </div>
            </div>

            <br>

            <?php
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if (!$conn) {
                echo "Connection Error.";
                exit;
            }
            // if a new task is being created, echos the drop down menu to select the project it will be assigned to.
            // the project of tasks being edited cannot be changed
            if (!isset($editingTask)) {
                // there is the text input that displays the name of the selected project, and a hidden input which holds the ID for computational use
                echo "<label class='mr-sm-2' for='projectsearch'>Select Project</label>
                <br>
                <div class='dropdown'>
                <input type='text' placeholder='Search..' id='projectsearch' class='searchbox form-control' style='width: 250px' onkeyup='filterFunction(\"project\")' required>
                <input type='hidden' id='hiddenprojectsearch' name='project' required>";   
                
                // managers can assign tasks to any project, team leader have to be leading the project.
                if ($_SESSION["role"] == "Manager") {
                    $sql = "SELECT project_title, project_ID FROM  project";
                }
                else {
                    $sql = "SELECT project_title, project_ID FROM  project where team_leader =".$_SESSION["user_ID"];
                }
                
                $result = mysqli_query($conn, $sql);
                
                if (!$result) {
                    echo "Connection Error.";
                    exit;
                }
                $projectsArray = mysqli_fetch_all($result);
                
                echo "<div id='projectDropdown' class='dropdown-content' style='width: 250px'>";
                
                // for each project a list element is echoed to display the project name and a hidden input which holds the project ID, linked with their ID's
                $i = 0;
                foreach ($projectsArray as $project) {
                    echo "<li id='project_li_$i' onmousedown='setSearch(\"project\", \"project_li_$i\")'>$project[0]</li>";
                    echo "<input type='hidden' id='id_project_li_$i' value='$project[1]'>";
                    $i++;
                }
                echo "</div></div><br>";
            }
            ?>

            <!-- the drop down to select the user the task will be assigned to, works the same way as the project drop down -->
            <label for="empsearch">Assign to Staff Member</label>
            <br>
            <div class="dropdown">
                <input type="text" placeholder="Search.." id="empsearch" class="searchbox form-control" style="width: 250px" onkeyup="filterFunction('emp')" required>
                <input type="hidden" id="hiddenempsearch" name="employee" required>
                <?php
                $sql = 'SELECT forename, surname, user_ID FROM users where role != "Manager"';
                

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    echo "Connection Error.";
                    exit;
                }
                $userArray = mysqli_fetch_all($result);
                ?>
                <div id="empDropdown" class="dropdown-content" style="width: 250px">
                    <?php
                    $i = 0;
                    foreach ($userArray as $user) {
                        echo "<li id='emp_li_$i' onmousedown='setSearch(\"emp\", \"emp_li_$i\")'>$user[0] $user[1]</li>";
                        echo "<input type='hidden' id='id_emp_li_$i' value='$user[2]'>";
                        if (isset($editTaskUser)) {
                            if ($user[2] == $editTaskUser) {
                                $setEmpTo = $i;
                            }
                        }
                        $i++;
                    }

                    ?>
                </div>
            </div>
            
            <!-- the confirm buttons, if a new task is being created, one button is created to confirm the task creation. 
                if a task is being edited then two are echod, one to confirm changes one to delete the task -->
            <div class="form-group row">
                <div class="col-sm-10">
                    <?php
                        if (!isset($editingTask)) {
                            echo '<button id="submitButton" name="submitButton" type="submit" class="btn btn-primary disabled">Assign Task</button>';
                        } else {
                            echo "<input type='hidden' name='edit_ID' value=$_GET[edit_ID]>";
                            echo '<button id="submitButton" name="confirmEditButton" type="submit" class="btn btn-primary disabled">Confirm Edit</button>';
                            echo '<button id="deleteButton" name="deleteButton" type="submit" class="btn btn-danger" style="margin-left: 15px;">Delete Task</button>';
                        }
                        ?>
                </div>
            </div>

        </form>
    </main>


    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top" style="padding-left: 25px; padding-right: 25px;">
        <p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

        <a href="/" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <img src="logo.png" alt="mdo" width="200" height="50">
            </svg>
        </a>

        <div class="justify-content-end">
            <p>Phone: 01509 888999</p>
            <p>Email: king@make‐it‐all.co.uk</p>
        </div>
    </footer>



    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

    <script>
        // searchable drop down functions

        // filters the displayed results in the dropdown based on what the user has typed in the input
        function filterFunction(dropdown) {
            document.getElementById(dropdown + 'search').classList.add("is-invalid");
            document.getElementById(dropdown + 'search').classList.remove("is-valid");
            document.getElementById("submitButton").classList.add("disabled");
            document.getElementById('hidden' + dropdown + 'search').value = null;

            var input, filter, ul, li, i;
            input = document.getElementById(dropdown + "search");
            filter = input.value.toUpperCase();
            div = document.getElementById(dropdown + "Dropdown");
            li = div.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                txtValue = li[i].textContent || li[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        // when a list element in the drop down is selected, set the text input and hidden input to the corresponding project title and ID
        function setSearch(dropdown, id) {
            document.getElementById('hidden' + dropdown + 'search').value = document.getElementById('id_' + id).value;
            document.getElementById(dropdown + 'search').value = document.getElementById(id).innerHTML;
            document.getElementById(dropdown + 'search').classList.add("is-valid");
            document.getElementById(dropdown + 'search').classList.remove("is-invalid");
            document.getElementById(dropdown + 'Dropdown').classList.remove("show");
            if (document.getElementById("empsearch").classList.contains("is-valid")) {
                if (document.getElementById("projectsearch").classList.contains("is-valid")) {
                    document.getElementById("submitButton").classList.remove("disabled");
                }
            }
        }

        // if the task is being edited rather than created, use the setSearch function to pre set the emp drop down 
        <?php if (isset($editingTask)) {echo "setSearch('emp', 'emp_li_$setEmpTo')";}?>
    </script>

</body>
