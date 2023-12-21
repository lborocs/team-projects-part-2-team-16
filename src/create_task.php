<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $empID = $_POST["employee"];
        $projectID = $_POST["project"];
        $title = $_POST["title"];
        $description = $_POST["description"];
        $date = $_POST["date"];
        $hours = $_POST["manhours"];
        
        
        include "db_connection.php";
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            echo "Connection Error.";
            exit;
        }
        $result = mysqli_query($conn, "select max(task_ID) from tasks;");
        $maxID = mysqli_fetch_row($result)[0];
        if ($maxID == null) {
            $ID = 1;
        } else {
            $ID = $maxID[0] + 1;
        }

        $insertQuery = "insert into tasks (task_ID, user_ID, project_ID, title, description, due_date, est_hours)
                                 values ($ID, $empID, $projectID, '$title', '$description', DATE '$date', $hours);";

        if (mysqli_query($conn, $insertQuery))  {
            header("location: dashboard.php");
            die();
        } else {
            echo "<script>alert('request unsucessful');</script>";
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
    <title>Assign Task</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">



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

    <link rel="stylesheet" href="./headers.css">
    <link rel="stylesheet" href="./searchable_dropdown.css">
</head>

<body>
    <?php
    session_start();
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
    }
    ?>



    <main class="container" style="margin:50px; flex: 70%;">
        <h1 class="my-5">Assign Task</h1>
        <form autocomplete="off" method="post" action="">
            
            <div class="form-group row">
                <label for="title" class="col-auto-2 col-form-label" style="margin-left: 0px; margin-right: 0px;">Task Name</label>
                <div>
                    <input type="title" name="title" class="form-control" id="title" placeholder="..." required>
                </div>
            </div>

            <div class="form-group row" style="margin-left: 0px; margin-right: 0px;">
                <label for="description" style="padding-left: 0px;">Task Description</label>
                <textarea class="form-control" id="description" name="description" rows="10" placeholder="..." required></textarea>
            </div>

            <div style="display: flex;">
                <div style="flex-direction: row;">
                    <label for="manhours">Estimated Man Hours</label>
                    <input type="number" id="manhours" name="manhours" class="form-control" placeholder="Hours" style="width: 200px;" min="1" required>
                </div>
                <div style="flex-direction: row; margin-left: 50px;">
                    <label for="date">Select Due Date</label>
                    <input class="form-control" id="date" name="date" type="date" style="width: 200px;" required>
                    <script>    
                        let date = new Date(); 
                        document.getElementById("date").min = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
                    </script>
                </div>
            </div>

            <br>

            <label class="mr-sm-2" for="projectsearch">Select Project</label>
            <br>
            <div class="dropdown">
                <input type="text" placeholder="Search.." id="projectsearch" class="searchbox form-control" onkeyup="filterFunction('project')" required>
                <input type="hidden" id="hiddenprojectsearch" name="project">
                <?php
                include "db_connection.php";
                $conn = mysqli_connect($servername, $username, $password, $dbname);
                if (!$conn) {
                    echo "Connection Error.";
                    exit;
                }
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
                ?>
                <div id="projectDropdown" class="dropdown-content">
                    <?php
                    $i = 0;
                    foreach ($projectsArray as $project) {
                        echo "<li id='project_li_$i' onmousedown='setSearch(\"project\", \"project_li_$i\")'>$project[0]</li>";
                        echo "<input type='hidden' id='id_project_li_$i' value='$project[1]'>";
                        $i++;
                    }
                    ?>
                </div>
            </div>

            <br>

            <label for="empsearch">Assign to Staff Member</label>
            <br>
            <div class="dropdown">
                <input type="text" placeholder="Search.." id="empsearch" class="searchbox form-control" onkeyup="filterFunction('emp')" required>
                <input type="hidden" id="hiddenempsearch" name="employee">
                <?php
                if ($_SESSION["role"] == "TL") {
                    $sql = "SELECT forename, surname, user_ID FROM users where role = 'Employee' or user_ID = ". $_SESSION["user_ID"];
                }
                else if ($_SESSION["role"] == "Manager") {
                    $sql = 'SELECT forename, surname, user_ID FROM users where role != "Manager"';
                }

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    echo "Connection Error.";
                    exit;
                }
                $userArray = mysqli_fetch_all($result);
                ?>
                <div id="empDropdown" class="dropdown-content">
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
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Assign</button>
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
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


    <script>
        // searchable drop downs
        function filterFunction(dropdown) {
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
            document.getElementById(dropdown + 'Dropdown').classList.remove("show");
        }
    </script>

</body>