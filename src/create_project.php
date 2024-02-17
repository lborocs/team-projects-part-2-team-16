<?php
    session_start();
    include "add_task.php";

    function create_project() {
        if (isset($_POST["proj_title"])) {
            $projTitle = $_POST["proj_title"];
            if (strlen($projTitle) > 255) {
                return false;
            }
        } else {
            return false;
        }
        if (isset($_POST["proj_description"])) {
            $projDescription = $_POST["proj_description"];
            if (strlen($projDescription) > 1000) {
                return false;
            }
        } else {
            return false;
        }
        if (isset($_POST["proj_date"])) {
            $projDate = $_POST["proj_date"];
            if (!date_create_from_format("Y-m-d", $projDate)) {
                return false;
            }
        } else {
            return false;
        }
        if (isset($_POST["proj_leader"])) {
            $projLeader = $_POST["proj_leader"];
            if (!is_numeric($projLeader)) {
                return false;
            } else {
                $projLeader = intval($projLeader);
            }
        } else {
            return false;
        }
        if (isset($_POST["taskBuffer"])) {
            $tasksJson = json_decode($_POST["taskBuffer"], true);
        } 

        // include "db_connection.php";
        // $conn = mysqli_connect($servername, $username, $password, $dbname);
        // if (!$conn) {
        //     echo "Connection Error.";
        //     exit;
        // }
        try {
            include "db_connection.php";

            $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
        } catch (PDOException $e) {
            echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
            return false;
        }

        $result = $conn->query("SELECT max(project_ID) FROM project");
        $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
        if ($maxID == null) {
            $projID = 1;
        } else {
            $projID = $maxID[0] + 1;
        }

        $proj_create_stmt = $conn->prepare("INSERT into project (project_ID, team_leader, project_title, due_date, description)
                                 values (:projID, :projLeader, :projTitle, DATE :projDate, :projDescription);");
        $proj_create_stmt->bindParam(':projID', $projID, PDO::PARAM_INT);
        $proj_create_stmt->bindParam(':projLeader', $projLeader, PDO::PARAM_INT);
        $proj_create_stmt->bindParam(':projTitle', $projTitle, PDO::PARAM_STR);
        $proj_create_stmt->bindParam(':projDate', $projDate, PDO::PARAM_STR);
        $proj_create_stmt->bindParam(':projDescription', $projDescription, PDO::PARAM_STR);
        if (!$proj_create_stmt->execute())  {
            echo "<script>alert('request unsucessful');</script>";
            header("location: dashboard.php");
        }

        $result = $conn->query("SELECT max(topic_ID) FROM topics");
        $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
        if ($maxID == null) {
            $topicID = 1;
        } else {
            $topicID = $maxID[0] + 1;
        }
        $date = date("Y-m-d");
        $create_topic_stmt = $conn->prepare("INSERT INTO topics (topic_ID, title, Date, views, project_ID) VALUES (:topicID, :title, DATE :date, 0, :project_ID)");
        $create_topic_stmt->bindParam(':topicID', $topicID, PDO::PARAM_INT);
        $create_topic_stmt->bindParam(':title', $projTitle, PDO::PARAM_STR);
        $create_topic_stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $create_topic_stmt->bindParam(':project_ID', $projID, PDO::PARAM_INT);
        $create_topic_stmt->execute();  

        $result = $conn->query("SELECT max(task_ID) FROM tasks");
        $maxID = $result->fetchAll(PDO::FETCH_NUM)[0];
        if ($maxID == null) {
            $taskID = 1;
        } else {
            $taskID = $maxID[0] + 1;
        }
        // task json format: {ID: maxID+1, title: taskTitle, description: taskdescription, hours: taskmanhours, duedate: taskduedate, empID: taskempID, empName: taskempName};
        if (!$tasksJson) {
            header("location: dashboard.php");
            die();
        }
        for ($i=0; $i < count($tasksJson); $i++) { 
            $task = $tasksJson[$i];
            $check = validate_title_desc($task["title"], $task["description"]);
            if (!$check[0]) {
                echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
                return;
            }
            $title = $task["title"];
            $description = $task["description"];

            $check = validate_date_time($task["duedate"], $task["hours"]);
            if (!$check[0]) {
                echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
                return;
            }
            $date = $task["duedate"];
            $hours = intval($task["hours"]);

            $check = validate_user($task["empID"]);
            if (!$check[0]) {
                echo "<script>alert('Failed to create the task. One of your input violated input requirments: ".$check[1]."');</script>";
                return;
            }
            $empID = intval($task["empID"]);


            // if (isset($task["title"])){
            //     $title = $task["title"];
            //     if (strlen($title) > 255) {
            //         echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //         continue;
            //     }
            // } else {
            //     echo "<script type='text/javascript'>alert('Failed to create a task, invalid data entered');</script>";
            //     continue;
            // }
            // if (isset($task["empID"])){
            //     $empID = $task["empID"];
            //     if (!is_numeric($empID)) {
            //         echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //         continue;
            //     } else {
            //         $empID = intval($empID);
            //     }
            // } else {
            //     echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //     continue;
            // }
            // if (isset($task["description"])) {
            //     $description = $task["description"];
            //     if (strlen($description) > 1000) {
            //         echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //         continue;
            //     }
            // } else {
            //     echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //     continue;
            // }
            // if (isset($task["duedate"])) {
            //     $date = $task["duedate"];
            //     if (!date_create_from_format("Y-m-d", $date)) {
            //         echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //         continue;
            //     }
            // } else {
            //     echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //     continue; 
            // }
            // if (isset($task["hours"])) {
            //     $hours = $task["hours"];
            //     if (!is_numeric($hours)) {
            //         echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //         continue;
            //     }else {
            //         $hours = intval($hours);
            //     }
            // } else {
            //     echo "<script type='text/javascript'>alert('Failed to create task: $title, invalid data entered');</script>";
            //     continue;
            // }

            $stmt = $conn->prepare("INSERT into tasks (task_ID, user_ID, project_ID, title, description, due_date, est_hours, progress) 
                                        VALUES (:taskID, :empID, :projectID, :title, :description, DATE :date, :hours, 0)");
            $stmt->bindParam(':taskID', $taskID, PDO::PARAM_INT);
            $stmt->bindParam(':empID', $empID, PDO::PARAM_INT);
            $stmt->bindParam(':projectID', $projID, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);

            if (!$stmt->execute())  {
                echo "<script type='text/javascript'>alert('request unsucesfull');</script>";
            }
            $taskID++;   
        }

        header("location: dashboard.php");
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!create_project()) {
            echo "<script type='text/javascript'>alert('Failed to create the task. One of your input violated input requirments');</script>";
        }
    }

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="./logo.ico">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project</title>

    <!-- <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
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
                $(this).addClass("text-light");
            }
            if ($(this).hasClass("tasksection") || $(this).parents('div.tasksection').length) {
                $(this).addClass("bg-dark-task");
            } else {
                $(this).addClass("bg-dark");
            }
             
        });
    }
})
</script>

<body>
    <?php
    if (!isset($_SESSION["role"])) {
        echo "<script>window.location.href='./login.php'</script>";
    } else if ($_SESSION["role"] == "Manager") {
        $taskcreate = "link-dark";
        $topicview = "link-dark";
        $dashview = "link-dark";
        include "./navbar_m.php";
    } else {
        header("location: dashboard.php");
    }
    ?>

    <main>
        <form autocomplete="off" method="post" action="">
            <div class="section">
                <h1 class="my-5">Create New Project</h1>
                <div class="form-group row">
                    <label for="proj_title" class="col-form-label" style="margin-right: 0px;">Title</label>
                    <div>
                        <input type="title" class="form-control" id="proj_title" name="proj_title" placeholder="..." onkeyup="isDuplicateTitle()" required>
                        <div id="duplicateTitleWarning" class="alert alert-warning alert-dismissible" style="display: none;">
                            <strong>Carefull!</strong> Project with this title already exists.
                        </div>
                    </div>
                </div>

                <div class="form-group row" style="margin-left: 0px; margin-right: 0px;">
                    <label for="proj_description" style="padding-left: 0px;">Project Description</label>
                    <textarea class="form-control" id="proj_description" name="proj_description" rows="10" placeholder="..." required></textarea>
                </div>


                <label for="proj_date">Estimated Completion Date</label>
                <input class="form-control" id="proj_date" name="proj_date" type="date" style="width: 250px;" required>
                <script>
                    let date = new Date();
                    document.getElementById("proj_date").min = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
                </script>

                <br>

                <label class="mr-sm-2" for="empsearch">Select Team Leader</label>
                <br>
                <div class="dropdown">
                    <input type="text" placeholder="Search.." id="TLsearch" class="searchbox form-control"
                        onkeyup="filterFunction('TL')" style="width:250px;" required>
                    <input type="hidden" id="hiddenTLsearch" name="proj_leader" required>
                    <?php
                    include "db_connection.php";
                    $conn = mysqli_connect($servername, $username, $password, $dbname);
                    if (!$conn) {
                        echo "Connection Error.";
                        exit;
                    }
                    $sql = 'SELECT forename, surname, user_ID FROM users where role = "TL"';
                    $result = mysqli_query($conn, $sql);

                    if (!$result) {
                        echo "Connection Error.";
                        exit;
                    }
                    $userArray = mysqli_fetch_all($result);
                    ?>
                    <div id="TLDropdown" class="dropdown-content" style="width: 250px;">
                        <?php
                        $i = 0;
                        foreach ($userArray as $user) {
                            echo "<li id='TL_li_$i' onmousedown='setSearch(\"TL\", \"TL_li_$i\")'>$user[0] $user[1]</li>";
                            echo "<input type='hidden' id='id_TL_li_$i' value='$user[2]'>";
                            $i++;
                        }
                        ?>
                    </div>
                </div>
            </div>

            <br>
            <br>

            <!-- add task form -->
            <div class="tasksection section">
                <h2 style="padding: 10px 10px 10px 10px;">Add Inital Tasks</h2>
                <hr style="margin: 0px 0px 12px 0px;">


                <label for="tasktitle" class="col-auto-2 col-form-label"
                    style="margin-left: 0px; margin-right: 0px;">Task Name</label>
                <input type="title" class="form-control" id="tasktitle" placeholder="...">

                <label for="taskdescription" style="padding-left: 0px;">Task Description</label>
                <textarea class="form-control" id="taskdescription" rows="10"
                    placeholder="..."></textarea>

                <div style="display: flex;">
                    <div style="flex-direction: row;">
                        <label for="taskmanhours">Estimated Man Hours</label>
                        <input type="number" id="taskmanhours" class="form-control"
                            placeholder="Hours" style="width: 250px;" min="1">
                    </div>
                    <div style="flex-direction: row; margin-left: 50px;">
                        <label for="taskdate">Select Task Due Date</label>
                        <input class="form-control" id="taskdate" type="date" style="width: 250px;">
                        <script>
                            let taskdate = new Date();
                            document.getElementById("taskdate").min = taskdate.getFullYear() + "-" + (taskdate.getMonth() + 1) + "-" + taskdate.getDate();
                        </script>
                    </div>
                </div>

                <label for="empsearch">Assign to Staff Member</label>
                <br>
                <div class="dropdown">
                    <input type="text" placeholder="Search.." id="empsearch" class="searchbox form-control"
                        onkeyup="filterFunction('emp')" style="width: 250px;">
                    <input type="hidden" id="hiddenempsearch">
                    <?php
                    $sql = 'SELECT forename, surname, user_ID FROM users where role != "Manager"';
                    $result = mysqli_query($conn, $sql);
                    if (!$result) {
                        echo "Connection Error.";
                        exit;
                    }
                    $userArray = mysqli_fetch_all($result);
                    ?>
                    <div id="empDropdown" class="dropdown-content" style="width: 250px;">
                        <?php
                        $i = 0;
                        foreach ($userArray as $user) {
                            echo "<li id='emp_li_$i' onmousedown='setSearch(\"emp\", \"emp_li_$i\")'>$user[0] $user[1]</li>";
                            echo "<input type='hidden' id='id_emp_li_$i' value='$user[2]'>";
                            $i++;
                        }
                        ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10" style="margin-bottom: 15px;">
                        <button id="assingTaskButton" type="button" class="btn btn-primary" onclick="addTask()">Assign Task</button>
                    </div>
                </div>

            </div>
            <!-- end of addd task form -->

            <br>
            
            <div class="section">
                <h3 id="NoTasksLabel">There are currently no tasks created for this project.</h3>

                <div id="CreatedTasksAccordian" class="accordion" style="display:none;">
                    <div class="accordion-item">
                        <h2 class="accordion-header" style="padding: 10px;">Initial Tasks</h2>
                        <div id="CreatedTasksDiv" class="row horizontal-scroll flex-md-row flex-md-nowrap">

                        </div>
                    </div>
                </div>
                <input type="hidden" id="taskBufferInput" name="taskBuffer" value=""> 

                <br>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <button id="submitButton" type="submit" class="btn btn-primary">Create Project</button>
                    </div>
                </div>
            </div>
        </form>
    </main>


    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top" style="padding-left: 25px; padding-right: 25px;">
        <p class="col-md-4 mb-0 text-body-secondary">© The Make It All Company</p>

        <a href="/"
            class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <img src="logo.png" alt="mdo" width="200" height="50">
            </svg>
        </a>

        <div class="justify-content-end">
            <p>Phone: 01509 888999</p>
            <p>Email: king@make‐it‐all.co.uk</p>
        </div>
    </footer>

</body>

</html>
<script>

    function isDuplicateTitle() {
        <?php
            $sql = 'SELECT project_title FROM project';
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                echo "Connection Error.";
                exit;
            }
            $result = mysqli_fetch_all($result);
        ?>
        titles = <?php echo json_encode($result) ?>;
        // console.log(titles);
        typedTitle = document.getElementById("proj_title").value;
        for (let i = 0; i < titles.length; i++) {
            if (titles[i][0] == typedTitle) {
                document.getElementById("duplicateTitleWarning").style.display = "block";
                return;
            }
            
        }
        document.getElementById("duplicateTitleWarning").style.display = "none";

    }

    // searchable drop downns

    function filterFunction(dropdown) {
        document.getElementById(dropdown + 'search').classList.add("is-invalid");
        document.getElementById(dropdown + 'search').classList.remove("is-valid");
        document.getElementById('hidden' + dropdown + 'search').value = null;
        if (dropdown == "TL"){
            document.getElementById("submitButton").classList.add("disabled");
        } else if (dropdown == "emp"){
            document.getElementById("assingTaskButton").classList.add("disabled");
        }
        

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

    function setSearch(dropdown, id) {
        document.getElementById('hidden' + dropdown + 'search').value = document.getElementById('id_' + id).value;
        document.getElementById(dropdown + 'search').value = document.getElementById(id).innerHTML;
        document.getElementById(dropdown + 'search').classList.add("is-valid");
        document.getElementById(dropdown + 'search').classList.remove("is-invalid");
        document.getElementById(dropdown + 'Dropdown').classList.remove("show");
        if (dropdown == "TL"){
            document.getElementById("submitButton").classList.remove("disabled");
        } else if (dropdown == "emp"){
            document.getElementById("assingTaskButton").classList.remove("disabled");
        }
    }

    let taskBuffer = [];

    function compareDates(a, b) {
        if (a.duedate == b.duedate) {
            return 0;
        } else if (a.duedate > b.duedate) {
            return 1;
        } else {
            return -1;
        }
    }

    function CreateTaskCard(task) {
        //<div class="col-12 col-md-4 col-lg-3 mb-3 mb-md-0">
        //    <div class="card card-body taskcard task-in-progress">
        //        <h5 class="card-title">${task.title}</h5>
        //        <h6 class="card-text"><b>${task.empName}</b></h6>
        //        <p class="card-text">${task.description}</p>
        //        <p class="card-text"><small class="text-muted">${task.dueDate}</small>
        //        </p>
        //    </div>
        //</div>;

        let CreatedTaskDiv = document.getElementById("CreatedTasksDiv");

        let newCard = document.createElement("div");
        newCard.classList.add("col-12", "col-md-4", "col-lg-3", "mb-3", "mb-md-0");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {newCard.classList.add("text-light", "bg-dark")}

        let cardBody = document.createElement("div");
        cardBody.classList.add("card", "card-body", "taskcard");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {cardBody.classList.add("text-light", "bg-dark")}
        cardBody.style.marginLeft = "10px";

        let cardTitle = document.createElement("h5");
        cardTitle.innerHTML = task.title;
        cardTitle.classList.add("card-title");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {cardTitle.classList.add("text-light", "bg-dark")}
        cardBody.appendChild(cardTitle);

        let cardDesc = document.createElement("p");
        cardDesc.innerHTML = task.description;
        cardDesc.classList.add("card-text");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {cardDesc.classList.add("text-light", "bg-dark")}
        cardBody.appendChild(cardDesc);

        let cardEmp = document.createElement("h6");
        cardEmp.innerHTML = task.empName;
        cardEmp.classList.add("card-title");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {cardEmp.classList.add("text-light", "bg-dark")}
        cardBody.appendChild(cardEmp);

        let cardHours = document.createElement("p");
        cardHours.classList.add("card-text", "mb-0", "mt-auto");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {cardHours.classList.add("text-light", "bg-dark")}
        let smallText2 = document.createElement("small");
        smallText2.classList.add("text-muted");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {smallText2.classList.add("text-light", "bg-dark")}
        smallText2.innerHTML = "Task Length: " + task.hours + " hour(s)";
        cardHours.appendChild(smallText2);
        cardBody.appendChild(cardHours);

        let cardDue = document.createElement("p");
        cardDue.classList.add("card-text");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {cardDue.classList.add("text-light", "bg-dark")}
        let smallText1 = document.createElement("small");
        smallText1.classList.add("text-muted");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {smallText1.classList.add("text-light", "bg-dark")}
        smallText1.innerHTML = "Due: " + task.duedate;
        cardDue.appendChild(smallText1);
        cardBody.appendChild(cardDue);

        let buttonsDiv = document.createElement("div");
        buttonsDiv.classList.add("row");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?> ) {buttonsDiv.classList.add("text-light", "bg-dark")}

        let editButton = document.createElement("button");
        editButton.innerHTML = "Edit";
        editButton.type = "button";
        editButton.onclick = function() {editTask(task.ID)}
        editButton.classList.add("btn", "btn-warning", "col-6");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {editButton.classList.add("text-light", "bg-dark")}
        buttonsDiv.appendChild(editButton);

        let deleteButton = document.createElement("button");
        deleteButton.innerHTML = "Remove";
        deleteButton.type = "button";
        deleteButton.onclick = function() {removeTask(task.ID)};
        deleteButton.classList.add("btn", "btn-danger", "col-6");
        if (<?php echo $_SESSION["lightmode"] ? 'true' : 'false' ?>) {deleteButton.classList.add("text-light", "bg-dark")}
        buttonsDiv.appendChild(deleteButton);

        cardBody.appendChild(buttonsDiv);

        

        newCard.appendChild(cardBody);
        CreatedTaskDiv.appendChild(newCard);
    }

    function removeTask(givenID) {
        let newTaskBuffer = [];
        for (let i = 0; i<taskBuffer.length; i++) {
            if (taskBuffer[i].ID != givenID) {
                newTaskBuffer.push(taskBuffer[i]);
            }
        } 
        taskBuffer = newTaskBuffer;
        displayTasks();
    }

    function editTask(givenID) {
        let task = null;
        for (let i = 0; i<taskBuffer.length; i++) {
            if (taskBuffer[i].ID == givenID) {
                task = taskBuffer[i];
            }
        } 

        document.getElementById("tasktitle").value = task.title;
        document.getElementById("taskdescription").value = task.description;
        document.getElementById("taskmanhours").value = task.hours;
        document.getElementById("taskdate").value = task.duedate;
        document.getElementById("hiddenempsearch").value = task.empID;
        document.getElementById("empsearch").value = task.empName;

        removeTask(givenID);

        document.getElementById("tasktitle").focus();
    }

    function addTask() {
        let taskTitle = document.getElementById("tasktitle").value;
        let taskdescription = document.getElementById("taskdescription").value;
        let taskmanhours = document.getElementById("taskmanhours").value;
        let taskduedate = document.getElementById("taskdate").value;
        let taskempID = document.getElementById("hiddenempsearch").value;
        let taskempName = document.getElementById("empsearch").value;

        if (taskTitle == null ||taskTitle == "") {
            document.getElementById("tasktitle").focus();
            return;
        }
        if (taskdescription == null ||taskdescription == "") {
            document.getElementById("taskdescription").focus();
            return;
        }
        if (taskmanhours == null ||taskmanhours == "") {
            document.getElementById("taskmanhours").focus();
            return;
        }
        if (taskduedate == null ||taskduedate == "") {
            document.getElementById("taskdate").focus();
            return;
        }
        if (taskempID == null ||taskempID == "") {
            document.getElementById("empsearch").focus();
            return;
        }

        let maxID = 0;
        for (let i = 0; i<taskBuffer.length; i++) {
            if (taskBuffer[i].ID > maxID) {
                maxID = taskBuffer[i].ID;
            }
        }

        const task = {ID: maxID+1, title: taskTitle, description: taskdescription, hours: taskmanhours, duedate: taskduedate, empID: taskempID, empName: taskempName};

        taskBuffer.push(task);
        taskBuffer.sort(compareDates);

        document.getElementById("tasktitle").value = null;
        document.getElementById("taskdescription").value = null;
        document.getElementById("taskmanhours").value = null;
        document.getElementById("taskdate").value = null;
        document.getElementById("hiddenempsearch").value = null;
        document.getElementById("empsearch").value = null;

        
        displayTasks();

    }

    function displayTasks() {
        document.getElementById("taskBufferInput").value = JSON.stringify(taskBuffer);
        if (taskBuffer.length != 0) {
            document.getElementById("NoTasksLabel").style.display = "none";
            document.getElementById("CreatedTasksAccordian").style.display = "block";
            let CreatedTaskDiv = document.getElementById("CreatedTasksDiv");
            CreatedTaskDiv.innerHTML = "";
            taskBuffer.forEach(CreateTaskCard);
        }
        else {
            document.getElementById("NoTasksLabel").style.display = "block";
            document.getElementById("CreatedTasksAccordian").style.display = "none";
        }

    }
</script>
